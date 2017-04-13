<?php

namespace CoderDojo\AdminBundle\Controller;

use CoderDojo\AdminBundle\Form\Type\ArticleType;
use CoderDojo\AdminBundle\Form\Type\CategoryType;
use CoderDojo\WebsiteBundle\Entity\Article;
use CoderDojo\WebsiteBundle\Entity\Category;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/nieuws/artikel")
 */
class ArticleController extends Controller
{
    /**
     * Lists all article entities.
     *
     * @Route("/", name="admin_blog_article_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $articles = $em->getRepository('CoderDojoWebsiteBundle:Article')->findAll();

        return $this->render('AdminBundle:Blog/Article:list.html.twig', [
            'articles' => $articles,
        ]);
    }

    /**
     * Creates a new article entity.
     *
     * @Route("/nieuw", name="admin_blog_article_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $form = $this->createForm(ArticleType::class);
        $form->remove('slug');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article = new Article(
                Uuid::uuid4()->toString(),
                $form->get('title')->getData(),
                $form->get('body')->getData(),
                'image.png',
                $form->get('publishedAt')->getData() ? new \DateTime($form->get('publishedAt')->getData()) : null,
                $this->getDoctrine()->getRepository(Category::class)->find($form->get('category')->getData()),
                $this->getUser()
            );

            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success', 'Artikel opgeslagen!');

            return $this->redirectToRoute('admin_blog_article_index');
        }

        return $this->render('AdminBundle:Blog/Article:new.html.twig', [
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

    /**
     * Delete a category
     *
     * @Route("/{id}/delete", name="admin_blog_category_delete")
     * @Method({"GET", "POST"})
     */
    public function deleteAction(Request $request, Category $category)
    {
        if (count($category->getArticles())) {
            $this->get('session')->getFlashBag()->add('error', 'Deze cetegorie bevat nog artikelen.');

            return $this->redirectToRoute('admin_blog_category_index');
        }

        if ($request->getMethod() === "POST") {
            $this->getDoctrine()->getManager()->remove($category);
            $this->getDoctrine()->getManager()->flush();

            $this->get('session')->getFlashBag()->add('success', 'De categorie is verwijderd!');

            return $this->redirectToRoute('admin_blog_category_index');
        }

        return $this->render('AdminBundle:Blog/Category:delete.html.twig', [
            'category' => $category
        ]);
    }
}
