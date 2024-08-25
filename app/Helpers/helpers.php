<?php

namespace App\Helpers;

use App\Models\Group;
use App\Models\Participant;
use Illuminate\Support\Str;

class helpers {

    public static function update_total_expenses(Group $group)
    {
        $total_expenses = 0;

        foreach($group->payments as $payment){
            $total_expenses += $payment->total;
        }

        $group->total_expenses = $total_expenses;
        $group->save();

        return true;
    }

    public static function calculate_balance(Group $group)
    {
        $debts = [];
        $balance = [];

        foreach ($group->payments as $payment) {
            $participantsWithoutExpenses = $payment->participants()->where('amount', 0)->get();
            $participantsWithExpenses = $payment->participants()->where('amount', '>', 0)->get();

            $participantsWithoutExpensesCount = $participantsWithoutExpenses->count();
            if ($participantsWithoutExpensesCount == 0) {
                continue; // skip this payment since no one to split debt among
            }

            foreach ($payment->contributors as $contributor) {
                $debtPerMember = ($contributor->amount - $participantsWithExpenses->sum('amount')) / $participantsWithoutExpensesCount;

                foreach ($participantsWithoutExpenses as $participant) {
                    if (!isset($debts[$participant->member->name][$contributor->member->name])) {
                        $debts[$participant->member->name][$contributor->member->name] = 0;
                    }
                    if ($contributor->member_id != $participant->member->id) {
                        $debts[$participant->member->name][$contributor->member->name] += $debtPerMember;
                    }
                }

                foreach ($participantsWithExpenses as $participant) {
                    if ($contributor->member_id != $participant->member_id) {
                        $debts[$participant->member->name][$contributor->member->name] += $participant->amount;
                    }
                }
            }
        }

        foreach ($debts as $from => $debt) {
            foreach ($debt as $to => $amount) {
                if ($from != $to) {
                    if (!isset($balance[$from][$to])) {
                        $balance[$from][$to] = 0;
                    }
                    if (!isset($balance[$to][$from])) {
                        $balance[$to][$from] = 0;
                    }
                    $balance[$from][$to] -= $amount;
                    $balance[$to][$from] += $amount;
                }
            }
        }

        return $balance;
    }

    public static function simplify_payment(Group $group)
    {
        // $balance = $this->calculate_balance($group);
    }

    public static function generate_reference_id($randcount, $string, $int)
    {
        $randomString = Str::random($randcount);
        $firstLetter = $string[0];
        $lastLetter = $string[strlen($string) - 1];
        $reference_id = $firstLetter . $lastLetter . $int . $randomString;

        return $reference_id;
    }

    public static function get_model($model, $reference_id){
        $get_model = $model::where('reference_id', $reference_id)->first();

        if(!$get_model){
            abort(404, [
                'message' => 'Model not found',
                'error_code' => 'MODEL_NOT_FOUND',
                'reference_id' => $reference_id
            ]);
        }

        return $get_model;
    }
}
