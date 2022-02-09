<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Command\CsvGeneratorCommand;
use App\Service\CsvProduction;
use App\Service\FilterTimeProduction;
use Symfony\Component\HttpKernel\KernelInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Vehicle;
use App\Entity\Expense;
use App\Entity\GasStation;
use App\Repository\ExpenseRepository;

class FilterCategoryController extends AbstractController
{
    /**
     * @Route("/expense", name="appexpense")
     *
     */
    public function appExpense(FilterTimeProduction $resultRequestCategory,Request $request): Response
    {
        $categoryArray=$this->getDoctrine()->getRepository('App\Entity\Expense')->findByCategory("diesel");


        $expenseArray=$this->getDoctrine()->getRepository('App\Entity\Expense')->findBy(array(), array('issuedOn' => 'ASC'));


        $expense=$resultRequestCategory->getFilter($request->request->get('date_begin'),$request->request->get('date_end'), $expenseArray);


        return $this->render('filterandcategory/expensesimple.html.twig', [
            'expense' => $expense,
        ]);
    }

    /**
     * @Route("/category", name="appcategory")
     *
     */
    public function appCategory(FilterTimeProduction $resultRequestCategory,Request $request): Response
    {

        // ok mais sans ASC
        $categoryArray=$this->getDoctrine()->getRepository('App\Entity\Expense')->findByCategory("diesel");


        $expenseArray=$this->getDoctrine()->getRepository('App\Entity\Expense')->findBy(array(), array('issuedOn' => 'ASC'));


        $expense=$resultRequestCategory->getFilterCategory($request->request->get('date_begin'),$request->request->get('date_end'),$request->request->get('category_value'), $expenseArray);


        return $this->render('filterandcategory/categoryexpence.html.twig', [
            'expense' => $expense,
        ]);
    }
}
