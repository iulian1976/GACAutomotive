<?php


namespace App\Service;


class Top10Vehicle
{   private $arrayVehicleExpense=[];

    public function SumAmountVehicle($arrayUnique, $arrayExpense)
    {
        $i=0;
        $event_name=0;
        $amountSumHT=0;
        $amountSumTTC=0;
        $count=0;
        foreach ($arrayUnique as $key1 =>$result1) {

            foreach ($arrayExpense as $key2 =>$result2) {

                if($result2->getVehicleId()==$result1->getVehicleId()) {

                    $i=$i+1;
                    $event_name=$result1;
                    $amountSumHT = $amountSumHT +$result2->getValueTe();
                    $amountSumTTC = $amountSumTTC +$result2->getValueTi();
                    $count=$i;
                }
            }
            $this->arrayVehicleExpense[$key1]["event_name"]=$event_name;
            $this->arrayVehicleExpense[$key1]["amountHT"]=$amountSumHT;
            $this->arrayVehicleExpense[$key1]["amountTTC"]=$amountSumTTC;
            $this->arrayVehicleExpense[$key1]["count"]=$count;

            $i=0;
        }
        return  $this->arrayVehicleExpense;
    }

    public function sortTenCustomers($arrayCustomerNet)
    {

        usort($arrayCustomerNet, function ($item1, $item2) {return $item2["amount"]  > $item1["amount"];});

        return $arrayCustomerNet;
    }
    public function sortFiveCustomersMoreTransaction($arrayCustomerNet)
    {
        usort($arrayCustomerNet, function ($item1, $item2) {return $item2["count"]  > $item1["count"];});
        //dd($arrayListCustomer);
        return $arrayCustomerNet;
    }

}