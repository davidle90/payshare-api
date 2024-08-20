<?php

namespace App\Helpers;

use App\Models\Group;
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
            // foreach ($payment->contributors as $contributor) {
            //     $debt = $contributor->amount / $payment->participants->count();
            //     foreach ($payment->participants as $participant) {
            //         if ($contributor->member_id != $participant->member_id) {
            //             $debts[$participant->name][$contributor->member->name] += $debt;
            //         } else {
            //             $debts[$participant->name][$contributor->member->name] += 0;
            //         }
            //     }
            // }

            foreach ($payment->contributors as $contributor) {
                $debt = $contributor->amount / $payment->participants->count();

                foreach ($payment->participants as $participant) {
                    if (!isset($debts[$participant->name])) {
                        $debts[$participant->name] = [];
                    }

                    if (!isset($debts[$participant->name][$contributor->member->name])) {
                        $debts[$participant->name][$contributor->member->name] = 0;
                    }

                    if ($contributor->member_id != $participant->member_id) {
                        $debts[$participant->name][$contributor->member->name] += $debt;
                    } else {
                        $debts[$participant->name][$contributor->member->name] += 0;
                    }
                }
            }
        }

        foreach ($debts as $from => $debt) {
            foreach ($debt as $to => $amount) {
                if($from != $to){
                    if (!isset($balance[$from][$to])) {
                        $balance[$from][$to] = 0;
                    }
                    $balance[$from][$to] += ($debts[$to][$from] ?? 0) - $amount;
                }
            }
        }

        foreach ($group->members as $member) {
            $debts[$member->name][$member->name] = 0;
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
}
