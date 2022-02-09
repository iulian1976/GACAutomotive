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

    public function getFilter($date_begin,$date_end,$categoryArray){

            $dataBeginArray=explode("/", $date_begin);
            $dataEndArray=explode("/", $date_end);
            $dataBeginArray[1]=intval($dataBeginArray[1]);
            $dataEndArray[1]=intval($dataEndArray[1]);

            $sommeHT=0;
            $sommeTVA=0;
            $sommeTTC=0;
            $intermedY=0;
            $intermedM=0;
            $intermedD=0;
            $q=0;



        foreach ($categoryArray as $key1 =>$result1) {

            $timeStringBDD=$result1->getIssuedOn()->format('Y-m-d'); //3
            $dataArrayBDD=explode("-",  $timeStringBDD);
            $dataArrayBDD[2]=intval($dataArrayBDD[2]);
            $dataArrayBDD[1]=intval($dataArrayBDD[1]);

            $intermedY=$dataArrayBDD[0];
            $dataArrayBDD[0]=$dataArrayBDD[2];
            $dataArrayBDD[2]= $intermedY;


            if ($dataEndArray[2]>$dataArrayBDD[2]) {
                break;
            }

            if ($dataEndArray[2]==$dataArrayBDD[2]) {

               if ($dataArrayBDD[1]==$dataBeginArray[1] || ($dataArrayBDD[1]>$dataBeginArray[1] AND $dataArrayBDD[0]<$dataEndArray[0])) {

                       if($dataArrayBDD[0]>$dataBeginArray[0] AND $dataArrayBDD[0]<$dataEndArray[0]){

                           $sommeHT = $sommeHT+$result1->getValueTe();
                           $sommeTVA=$sommeTVA+$result1->getTaxRate();
                           $sommeTTC= $sommeTTC+$result1->getValueTi();
                       }

                }elseif ($dataArrayBDD[1]<$dataEndArray[1] AND $dataArrayBDD[1]>$dataBeginArray[1] ){

                           $sommeHT = $sommeHT +$result1->getValueTe();
                           $sommeTVA=$sommeTVA+ $result1->getTaxRate();
                           $sommeTTC= $sommeTTC+$result1->getValueTi();
               }

                if ($dataArrayBDD[1]==$dataEndArray[1] AND $dataArrayBDD[0]>$dataEndArray[0]) {
                           $q=1;
                           break;
                }
            }

        }

        $this->dataExpense[0]=$sommeHT;
        $this->dataExpense[1]=$sommeTVA;
        $this->dataExpense[2]=$sommeTTC;

        return $this->dataExpense;

    }

    public function getFilterCategory($date_begin,$date_end,$category_value,$categoryArray){

        $dataBeginArray=explode("/", $date_begin);
        $dataEndArray=explode("/", $date_end);
        $dataBeginArray[1]=intval($dataBeginArray[1]);
        $dataEndArray[1]=intval($dataEndArray[1]);

        $sommeHT=0;
        $sommeTVA=0;
        $sommeTTC=0;
        $intermedY=0;
        $intermedM=0;
        $intermedD=0;
        $q=0;



        foreach ($categoryArray as $key1 =>$result1) {

          if($result1->getCategory()==$category_value){

                    $timeStringBDD=$result1->getIssuedOn()->format('Y-m-d'); //3
                    $dataArrayBDD=explode("-",  $timeStringBDD);
                    $dataArrayBDD[2]=intval($dataArrayBDD[2]);
                    $dataArrayBDD[1]=intval($dataArrayBDD[1]);

                    $intermedY=$dataArrayBDD[0];
                    $dataArrayBDD[0]=$dataArrayBDD[2];
                    $dataArrayBDD[2]= $intermedY;


                    if ($dataEndArray[2]>$dataArrayBDD[2]) {
                        break;
                    }

                    if ($dataEndArray[2]==$dataArrayBDD[2]) {

                        if ($dataArrayBDD[1]==$dataBeginArray[1] || $dataArrayBDD[1]>$dataBeginArray[1] ) {

                            if($dataArrayBDD[0]>$dataBeginArray[0] AND $dataArrayBDD[0]<$dataEndArray){

                                $sommeHT = $sommeHT+$result1->getValueTe();
                                $sommeTVA=$sommeTVA+$result1->getTaxRate();
                                $sommeTTC= $sommeTTC+$result1->getValueTi();
                            }

                        }elseif ($dataArrayBDD[1]< $dataEndArray[1]){

                            $sommeHT = $sommeHT + $result1->getValueTe();
                            $sommeTVA=$sommeTVA+ $result1->getTaxRate();
                            $sommeTTC= $sommeTTC+$result1->getValueTi();
                        }

                        if ($dataArrayBDD[1]==$dataEndArray[1] AND $dataArrayBDD[0]>$dataEndArray[0]) {
                            $q=1;
                            break;
                        }
                    }
          }

        }

        $this->dataExpense[0]=$sommeHT;
        $this->dataExpense[1]=$sommeTVA;
        $this->dataExpense[2]=$sommeTTC;
        $this->dataExpense[3]=$category_value;

        return $this->dataExpense;

    }



}