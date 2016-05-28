<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Blog;
/**
 * @Route("/hoge")
 */
class HogeController extends Controller
{
    /**
     * @Route("/")
     * @Method("get")
     */
    public function indexAction()
    {
        return $this->render('Hoge/index.html.twig',
            ['form' => $this->createHogeForm()->createView()]
        );
    }

    /**
     * @Route("/")
     * @Method("post")
     */
    public function indexPostAction(Request $request)
    {
        $form = $this->createHogeForm();
        $form->handleRequest($request);

        if (!$form->isValid()) {

            return $this->render('Hoge/index.html.twig',
                ['form' => $form->createView()]
            );
        }
        $name = $form->get('name')->getData();

        $blog = new Blog();
        $blog->setName($name);
        $blog->setUrl('hoge.co.jp');

        $em = $this->getDoctrine()->getManager();
        $em->persist($blog);
        $em->flush();

        return $this->redirect(
            $this->generateUrl('app_hoge_complete')
        );
    }

    /**
     * @Route("/complete")
     * @Method("get")
     */
    public function completeAction()
    {
        echo 'complete';
        $em = $this->getDoctrine()->getManager();
        $blogRepository = $em->getRepository('AppBundle:Blog');
        $records = $blogRepository->findBy([], ['id' => 'DESC']);
        var_dump($records);
        exit;
    }

    /**
     * @Route("/join")
     * @Method("get")
     */
    public function joinAction()
    {
        $con = $this->getDoctrine()->getEntityManager()->getConnection();
        $sql = 'select id, name, url from blog union select id, name, url from blog2';
        $statement = $con->prepare($sql);
        $statement->execute();

        $results = $statement->fetchAll();

        var_dump($results);
        exit;
    }

    private function createHogeForm()
    {
        return $this->createFormBuilder()
            ->add('name',  'text', [
                'label' => 'お名前',
            ])
            ->add('email', 'text')
            ->add('tel', 'text',[
                'required' => false
            ])
            ->add('type', 'choice', [
                'choices' => [
                    '公演について',
                    'その他'
                ],
                'expanded' => true,
            ])
            ->add('content', 'textarea')
            ->add('submit', 'submit', [
                'label' => '送信',
            ])
            ->getForm();
    }
}
