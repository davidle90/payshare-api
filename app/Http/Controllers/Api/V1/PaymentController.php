<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\helpers;
use App\Http\Filters\V1\PaymentFilter;
use App\Models\Payment;
use App\Http\Requests\Api\V1\StorePaymentRequest;
use App\Http\Requests\Api\V1\UpdatePaymentRequest;
use App\Http\Resources\V1\PaymentResource;
use App\Models\Contributor;
use App\Models\Group;
use App\Models\Participant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class PaymentController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index(PaymentFilter $filters)
    {
        return PaymentResource::collection(Payment::filter($filters)->paginate());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePaymentRequest $request, $group_reference_id)
    {
        $group = Group::where('reference_id', $group_reference_id)->first();

        if(!$group){
            return $this->error('Group not found', 404);
        }

        if(Gate::authorize('store-payment', $group)){

            $attributes = $request->mappedAttributes();
            $attributes['group_id'] = $group->id;
            $attributes['created_by'] = Auth::user()->id;

            $payment = Payment::create($attributes);
            $payment->reference_id = helpers::generate_reference_id(3, $payment->label, $payment->id);
            $payment->save();

            foreach($attributes['participants'] as $participant) {
                $new_participant = Participant::firstOrNew(['member_id' => $participant['id'], 'payment_id' => $payment->id]);
                $new_participant->amount = $participant['amount'] ?? 0;
                $new_participant->save();
            }

            foreach($attributes['contributors'] as $contributor) {
                $new_contributor = Contributor::firstOrNew(['member_id' => $contributor['id'], 'payment_id' => $payment->id]);
                $new_contributor->amount = $contributor['amount'] ?? 0;
                $new_contributor->save();
            }

            $total = 0;
            foreach($payment->contributors as $payment_contributor){
                $total += $payment_contributor->amount;
            }

            $payment->total = $total;
            $payment->reference_id = helpers::generate_reference_id(5, $payment->label, $payment->id);
            $payment->save();

            helpers::calculate_balance($group);
            helpers::update_total_expenses($group);

            return new PaymentResource($payment);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($group_reference_id, $payment_reference_id)
    {
        $group = Group::where('reference_id', $group_reference_id)->first();
        $payment = Payment::where('reference_id', $payment_reference_id)->first();

        if (!$group || !$payment) {
            $missingModel = !$group ? 'Group' : 'Payment';
            return $this->error("$missingModel not found", 404);
        }

        if(Gate::authorize('show-payment', $group)){

            if($this->include('group')) {
                $payment->load('group');
            }

            if($this->include('participants')) {
                $payment->load('participants');
            }

            if($this->include('contributors')) {
                $payment->load('contributors');
            }

            return new PaymentResource($payment);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePaymentRequest $request, $group_reference_id, $payment_reference_id)
    {
        $group = Group::where('reference_id', $group_reference_id)->first();
        $payment = Payment::where('reference_id', $payment_reference_id)->first();

        if (!$group || !$payment) {
            $missingModel = !$group ? 'Group' : 'Payment';
            return $this->error("$missingModel not found", 404);
        }

        if(Gate::authorize('payment-group', [$group, $payment])) {

            $attributes = $request->mappedAttributes();
            $participant_ids = [];
            $contributor_ids = [];

            $payment->update($attributes);

            $group_member_ids = $group->members()->pluck('member_id')->toarray();

            foreach($attributes['contributors'] as $contributor){
                $contributor_ids[] = $contributor['id'];

                if(!in_array($contributor['id'], $group_member_ids)){
                    return $this->error('User not member of this group: '.$contributor['id'], 400);
                }
            }

            foreach($attributes['participants'] as $participant){
                $participant_ids[] = $participant['id'];

                if(!in_array($participant['id'], $group_member_ids)){
                    return $this->error('User not member of this group: '.$participant['id'], 400);
                }
            }

            foreach($payment->contributors as $payment_contributor){
                if(!in_array($payment_contributor->id, $contributor_ids)){
                    $payment_contributor->delete();
                }
            }

            foreach($attributes['contributors'] as $contributor) {
                $new_contributor = Contributor::firstOrNew(['member_id' => $contributor['id'], 'payment_id' => $payment->id]);
                $new_contributor->amount = $contributor['amount'] ?? 0;
                $new_contributor->save();
            }

            foreach($payment->participants as $payment_participant){
                if(!in_array($payment_participant->id, $participant_ids)){
                    $payment_participant->delete();
                }
            }

            foreach($attributes['participants'] as $participant) {
                $new_participant = Participant::firstOrNew(['member_id' => $participant['id'], 'payment_id' => $payment->id]);
                $new_participant->amount = $participant['amount'] ?? 0;
                $new_participant->save();
            }

            $payment->refresh();
            $total = $payment->contributors->sum('amount');

            $payment->total = $total;
            $payment->save();

            helpers::calculate_balance($group);
            helpers::update_total_expenses($group);

            return new PaymentResource($payment);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($group_reference_id, $payment_reference_id)
    {
        $group = Group::where('reference_id', $group_reference_id)->first();
        $payment = Payment::where('reference_id', $payment_reference_id)->first();

        if (!$group || !$payment) {
            $missingModel = !$group ? 'Group' : 'Payment';
            return $this->error("$missingModel not found", 404);
        }

        if(Gate::authorize('delete-payment', $group)) {

            $payment->contributors()->delete();
            $payment->participants()->delete();
            $payment->delete();

            helpers::calculate_balance($group);
            helpers::update_total_expenses($group);

            return $this->ok('Payment successfully deleted.');
        }
    }
}
