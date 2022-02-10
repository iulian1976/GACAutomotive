<?php


namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\HttpKernel\KernelInterface;
use App\Entity\Expense;
use App\Entity\Vehicle;
use App\Entity\GasStation;
use Symfony\Component\Validator\Constraints\DateTime;
use CrEOF\Spatial\PHP\Types\Geometry\Point;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Command\CsvGeneratorCommand;
use App\Service\CsvProduction;
use App\Repository\ExpenseRepository;


class FilterTimeProduction
{
    private $dataExpense=[];
    private $categoryArray=[];
    private $expenseArray=[];
    private  $sommeHT=0;
    private  $sommeTVA=0;
    private  $sommeTTC=0;


    public function getFilter($date_begin,$date_end,$categoryArray){

         $this->categoryArray=$categoryArray;

         $dataBeginArray=explode("/", $date_begin);
         $dataEndArray=explode("/", $date_end,);

         $t2=$this->inversDayMonthYear($dataBeginArray);
         $t3=$this->inversDayMonthYear($dataEndArray);

         $tbegin=$this->mixArrayToString($t2);
         $tend=$this->mixArrayToString($t3);

         $now = new DateTime();

         $secTimeBegin=time()-strtotime($tbegin);
         $sectTimeEnd=time()-strtotime($tend);


        foreach ($categoryArray as $key1 =>$result1) {

            $timeStringBDD =$result1->getIssuedOn()->format('Y-m-d');

            $sectimeBdd1=time()-strtotime($timeStringBDD);

            if($sectimeBdd1<=$secTimeBegin AND $sectimeBdd1>=$sectTimeEnd){

                $this->sommeHT = $this->sommeHT+$result1->getValueTe();
                $this->sommeTVA=$this->sommeTVA+$result1->getTaxRate();
                $this->sommeTTC= $this->sommeTTC+$result1->getValueTi();
            }
        }



        $this->dataExpense[0]=$this->sommeHT;
        $this->dataExpense[1]=$this->sommeTVA;
        $this->dataExpense[2]=$this->sommeTTC;

        return $this->dataExpense;
    }

    public function getFilterCategory($date_begin,$date_end,$category_value,$categoryArray){

        $this->categoryArray=$categoryArray;

        $dataBeginArray=explode("/", $date_begin);
        $dataEndArray=explode("/", $date_end,);

        $t2=$this->inversDayMonthYear($dataBeginArray);
        $t3=$this->inversDayMonthYear($dataEndArray);

        $tbegin=$this->mixArrayToString($t2);
        $tend=$this->mixArrayToString($t3);

        $now = new DateTime();

        $secTimeBegin=time()-strtotime($tbegin);
        $sectTimeEnd=time()-strtotime($tend);

        foreach ($categoryArray as $key1 =>$result1) {

          if($result1->getCategory()==$category_value){

              $timeStringBDD =$result1->getIssuedOn()->format('Y-m-d');

              $sectimeBdd1=time()-strtotime($timeStringBDD);

              if($sectimeBdd1<=$secTimeBegin AND $sectimeBdd1>=$sectTimeEnd){

                  $this->sommeHT = $this->sommeHT+$result1->getValueTe();
                  $this->sommeTVA=$this->sommeTVA+$result1->getTaxRate();
                  $this->sommeTTC= $this->sommeTTC+$result1->getValueTi();

              }


          }

        }

        $this->dataExpense[0]=$this->sommeHT;
        $this->dataExpense[1]=$this->sommeTVA;
        $this->dataExpense[2]=$this->sommeTTC;
        $this->dataExpense[3]=$category_value;


        return $this->dataExpense;
    }

    public function getVehicleFilter($date_begin,$date_end,$topVehicle,$expenseArray){

        $this->expenseArray= $expenseArray;

        $dataBeginArray=explode("/", $date_begin);
        $dataEndArray=explode("/", $date_end,);

        $t2=$this->inversDayMonthYear($dataBeginArray);
        $t3=$this->inversDayMonthYear($dataEndArray);

        $tbegin=$this->mixArrayToString($t2);
        $tend=$this->mixArrayToString($t3);

        $now = new DateTime();

        $secTimeBegin=time()-strtotime($tbegin);
        $sectTimeEnd=time()-strtotime($tend);


        foreach ($this->expenseArray as $key1 =>$result1) {

            $timeStringBDD =$result1->getIssuedOn()->format('Y-m-d');

            $sectimeBdd1=time()-strtotime($timeStringBDD);

            if($sectimeBdd1<=$secTimeBegin AND $sectimeBdd1>=$sectTimeEnd){

                dd($this->expenseArray[0]);

                $this->sommeHT = $this->sommeHT+$result1->getValueTe();
                $this->sommeTVA=$this->sommeTVA+$result1->getTaxRate();
                $this->sommeTTC= $this->sommeTTC+$result1->getValueTi();
            }
        }



        $this->dataExpense[0]=$this->sommeHT;
        $this->dataExpense[1]=$this->sommeTVA;
        $this->dataExpense[2]=$this->sommeTTC;

        return $this->dataExpense;
    }






    public function inversDayMonthYear($dataArray){

        $intermed=$dataArray[2];
        $dataArray[2]=$dataArray[0];
        $dataArray[0]=$intermed;

        return $dataArray;

    }

    public function  mixArrayToString($dataArray){

        $dataString=$dataArray[0].'-'.$dataArray[1].'-'.$dataArray[2];

        return $dataString;

    }



}