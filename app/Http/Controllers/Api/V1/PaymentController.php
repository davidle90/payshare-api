<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Filters\V1\PaymentFilter;
use App\Models\Payment;
use App\Http\Requests\Api\V1\StorePaymentRequest;
use App\Http\Requests\Api\V1\UpdatePaymentRequest;
use App\Http\Resources\V1\PaymentResource;
use App\Models\Group;
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
    public function store(StorePaymentRequest $request, Group $group)
    {

        if(Gate::authorize('store-payment', $group)){

            $attributes = $request->mappedAttributes();
            $attributes['group_id'] = $group->id;
            $attributes['created_by'] = Auth::user()->id;

            $payment = Payment::create($attributes);
            $payment->save();

            return new PaymentResource($payment);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Group $group, Payment $payment)
    {
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
    public function update(UpdatePaymentRequest $request, Group $group, Payment $payment)
    {
        if(Gate::authorize('update-payment', $group)) {

            $payment->update($request->mappedAttributes());

            return new PaymentResource($payment);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Group $group, Payment $payment)
    {
        if(Gate::authorize('delete-payment', $group)) {

            $payment->contributors()->delete();
            $payment->participants()->detach();
            $payment->delete();

            return $this->ok('Payment successfully deleted.');
        }
    }
}
