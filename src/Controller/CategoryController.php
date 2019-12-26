<?php


namespace App\Controller;



use App\Form\CategoryType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Category;
use Symfony\Component\Form\Form;

class CategoryController extends AbstractController

{

    /**
     * @param $object
     */
    public function add($object){
        $em = $this->getDoctrine()->getManager();
        $em->persist($object);
        $em->flush();
    }


    /**
     * @Route ("/category/add", name="wild_ajout")
     * @param Request $request
     * @return Response
     */

    public function index(Request $request):Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        if($request->isMethod('POST')){
            $form->handleRequest($request);
            if($form->isSubmitted()){
                $data = $form->getData();
                $this->add($data);
            }
        }
        return $this->render(
            'wild/add.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

}