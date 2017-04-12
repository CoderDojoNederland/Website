<?php

namespace CoderDojo\AdminBundle\Controller;

use CoderDojo\AdminBundle\Form\Type\CategoryType;
use CoderDojo\WebsiteBundle\Entity\Category;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/nieuws/categorie")
 */
class CategoryController extends Controller
{
    /**
     * Lists all category entities.
     *
     * @Route("/", name="admin_blog_category_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $categories = $em->getRepository('CoderDojoWebsiteBundle:Category')->findAll();

        return $this->render('AdminBundle:Blog/Category:list.html.twig', [
            'categories' => $categories,
        ]);
    }

    /**
     * Creates a new category entity.
     *
     * @Route("/nieuw", name="admin_blog_category_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $form = $this->createForm(CategoryType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $category = new Category(Uuid::uuid4()->toString(), $form->get('title')->getData());

            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success', 'Category opgeslagen!');

            return $this->redirectToRoute('admin_blog_category_index');
        }

        return $this->render('AdminBundle:Blog/Category:new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Displays a form to edit an existing category entity.
     *
     * @Route("/{id}/edit", name="admin_blog_category_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Category $category)
    {
        $editForm = $this->createForm(CategoryType::class, $category);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $category->setTitle($editForm->get('title')->getData());

            $this->getDoctrine()->getManager()->flush();

            $this->get('session')->getFlashBag()->add('success', 'Categorie opgeslagen!');

            return $this->redirectToRoute('admin_blog_category_index');
        }

        return $this->render('AdminBundle:Blog/Category:edit.html.twig', array(
            'category' => $category,
            'edit_form' => $editForm->createView()
        ));
    }
}
