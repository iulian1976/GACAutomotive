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

class CsvProduction
{
    private $appKernel;
    private $transportObjects=[];
    private $data=[];
    private $nbrRow;

    public function getData(){
        return $this->data;
    }
    public function getNbrRow(){
        return $this->nbrRow;
    }

    public function verifyPeriod($firstDatePeriodBdd){

        $firstDatePeriodCsv=$this->data;

        if($firstDatePeriodBdd===$firstDatePeriodCsv[0]["Date & heure"]){
            return false;
        }
        else{
            return true;
        }

    }

    public function executeCSV(KernelInterface $appKernel){

        $this->appKernel=$appKernel;

        $projectRoot = $this->appKernel->getProjectDir();

        $inputFile=$projectRoot.'\var\generate\infinity_energy_test_20220204220236.csv';

        $serializer = new Serializer([new ObjectNormalizer()], [new CsvEncoder()]);
        // decoding CSV contents
        $this->data = $serializer->decode(file_get_contents($inputFile), 'csv',[CsvEncoder::DELIMITER_KEY => ';']);

        $this->nbrRow=count($this->data);

    }

    public function ArrayToObject(EntityManagerInterface $em){

       foreach ($this->data as $key1 =>$result1) {

       $vehicleObj = new Vehicle();
       $expenseObj = new Expense();
       $gasstationObj = new GasStation();

       $vehicleObj->setPlateNumber($result1["Immatriculation"]);
       $vehicleObj->setBrand($result1["Marque"]);
       $vehicleObj->setModel($result1["Model"]);

       if (str_contains( $result1["HT"], ',')) {
           $result1["HT"]=str_replace(',', '.', $result1["HT"]);
       }
       if (str_contains( $result1["TTC"], ',')) {
           $result1["TTC"]=str_replace(',', '.', $result1["TTC"]);
       }
       if (str_contains( $result1["TVA"], ',')) {
           $result1["TVA"]=str_replace(',', '.', $result1["TVA"]);
       }

       $expenseObj->setCategory($result1["Catégorie  de dépense"]);
       $expenseObj->setInvoiceNumber($result1["Numéro facture"]);
       $expenseObj->setValueTe($result1["HT"]);
       $expenseObj->setValueTi($result1["TTC"]);
       $expenseObj->setTaxRate($result1["TVA"]);
       $expenseObj->setIssuedOn(new \DateTime($result1["Date & heure"]));
       $expenseObj->setInvoiceNumber($result1["Numéro facture"]);
       $expenseObj->setExpenseNumber($result1["Code dépense"]);


       $gasstationObj->setDescription($result1["Station"]);
       $result1["Position GPS (Latitude) "];
       $result1["Position GPS (Longitude)"];
       $coordinates=$result1["Position GPS (Latitude) "]." ".$result1["Position GPS (Longitude)"];
       $gasstationObj->setCoordinate(new Point (  $result1["Position GPS (Latitude) "], $result1["Position GPS (Longitude)"]));

       $this->transportObjects[0]=$vehicleObj;
       $this->transportObjects[1]=$expenseObj;
       $this->transportObjects[2]= $gasstationObj;
        // dd( $vehicleObj);
        //dd($expenseObj);
        //print_r($expenseObj);
        //dd($expenseObj);
        //dd( $this->transportObjects);

       $em->persist( $this->transportObjects[0]);

       $em->flush();

       $vehicleIdForeignKey  =  $this->transportObjects[0]->getVehicleId();
       $this->transportObjects[1]->setVehicleId($vehicleIdForeignKey);
       $em->persist($this->transportObjects[1]);

       $em->flush();

       $expenseIdForeignKey=$this->transportObjects[1]->getExpenseId();
       $this->transportObjects[2]->setExpense($expenseIdForeignKey);

       $em->persist($this->transportObjects[2]);

       $em->flush();
       }

    }





}