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
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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

        $articles = $em->getRepository('CoderDojoWebsiteBundle:Article')->findBy([],['createdAt'=>'DESC']);

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
            $image = $this->uploadHeader($form->get('image')->getData());

            $article = new Article(
                Uuid::uuid4()->toString(),
                $form->get('title')->getData(),
                $form->get('body')->getData(),
                $image,
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
     * Displays a form to edit an existing article entity.
     *
     * @Route("/{id}/edit", name="admin_blog_article_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Article $article)
    {
        $editForm = $this->createForm(ArticleType::class, $article);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $image = $this->uploadHeader($editForm->get('image')->getData());
            $article->setImage($image);

            $this->getDoctrine()->getManager()->flush();

            $this->get('session')->getFlashBag()->add('success', 'Artikel opgeslagen!');

            return $this->redirectToRoute('admin_blog_article_index');
        }

        return $this->render('AdminBundle:Blog/Article:edit.html.twig', array(
            'article' => $article,
            'edit_form' => $editForm->createView()
        ));
    }

    /**
     * Delete a category
     *
     * @Route("/{id}/delete", name="admin_blog_article_delete")
     * @Method({"GET", "POST"})
     */
    public function deleteAction(Request $request, Article $article)
    {
        if ($request->getMethod() === "POST") {
            $this->getDoctrine()->getManager()->remove($article);
            $this->getDoctrine()->getManager()->flush();

            $this->get('session')->getFlashBag()->add('success', 'Het artikel is verwijderd!');

            return $this->redirectToRoute('admin_blog_article_index');
        }

        return $this->render('AdminBundle:Blog/Article:delete.html.twig', [
            'article' => $article
        ]);
    }

    /**
     * @Route("/{id}/publish", name="admin_blog_article_publish")
     * @Method({"GET"})
     */
    public function publishAction(Article $article)
    {
        $article->publish();
        $this->getDoctrine()->getManager()->flush();

        $this->get('session')->getFlashBag()->add('success', 'Het artikel is gepuliceerd!');

        return $this->redirectToRoute('admin_blog_article_index');
    }

    /**
     * @Route("/{id}/unpublish", name="admin_blog_article_unpublish")
     * @Method({"GET"})
     */
    public function unPublishAction(Article $article)
    {
        $article->unPublish();
        $this->getDoctrine()->getManager()->flush();

        $this->get('session')->getFlashBag()->add('success', 'Het artikel is offline!');

        return $this->redirectToRoute('admin_blog_article_index');
    }

    /**
     * @param UploadedFile $uploadedFile
     * @return string
     */
    private function uploadHeader(UploadedFile $uploadedFile)
    {
        $kernel = $this->get('kernel')->getRootDir();
        $destination = $kernel . '/../web/articles';

        $filesystem = new Filesystem();

        if(!$filesystem->exists($destination)) {
            $filesystem->mkdir($destination);
        }

        $uploadedFile->move($destination, $uploadedFile->getClientOriginalName());

        return $uploadedFile->getClientOriginalName();
    }
}
