<?php

namespace Kunstmaan\TranslatorBundle\Controller;

use Kunstmaan\TranslatorBundle\Form\TranslationAdminType;
use Kunstmaan\TranslatorBundle\Entity\Translation;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class TranslatorGeneralController extends Controller
{
    /**
     * @Route("/", name="KunstmaanTranslatorBundle_settings_translations")
     * @Template("KunstmaanTranslatorBundle:General:list.html.twig")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $translations = $em->getRepository('KunstmaanTranslatorBundle:Translation')->findAll();

        $cacheFresh = $this->get('kunstmaan_translator.service.translator.cache_validator')->isCacheFresh();
        $debugMode = $this->container->getParameter('kernel.debug') === true;

        if (!$cacheFresh && !$debugMode) {
            $noticeText = $this->get('translator')->trans('settings.translator.not_live_warning');
            $this->get('session')->getFlashBag()->add('notice', $noticeText);
        }

        return array(
            'translations' => $translations
        );
    }

    /**
     * The add action
     *
     * @Method({"GET", "POST"})
     * @Template("KunstmaanTranslatorBundle:General:addTranslation.html.twig")
     * @return array
     */
    public function addAction($keyword = '', $domain = '', $locale = '')
    {
        /* @var $em EntityManager */
        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();

        $translation = new Translation();
        $translation->setDomain($domain);
        $translation->setKeyword($keyword);
        $translation->setLocale($locale);

        $locales = $this->container->getParameter('kuma_translator.managed_locales');

        $choicesText = $this->get('translator')->trans('settings.translator.choose_language');
        $form = $this->createForm(new TranslationAdminType(), $translation);
        $form->add('locale', 'language', array('choices' => array_combine($locales, $locales), 'empty_value' => $choicesText));
        $form->add('domain', 'text');
        $form->add('keyword', 'text');

        if ('POST' == $request->getMethod()) {
            $form->bind($request);
            if ($form->isValid()) {
                $em->persist($translation);
                $em->flush();

                $this->get('session')->getFlashBag()->add('success', $this->get('translator')->trans('settings.translator.succesful_added'));

                return new RedirectResponse($this->generateUrl("KunstmaanTranslatorBundle_settings_translations"));
            }
        }

        return array(
            'form' => $form->createView()
        );
    }

    /**
     * @param $id
     *
     * @throws NotFoundHttpException
     * @internal param $eid
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/{id}/edit", requirements={"id" = "\d+"}, name="KunstmaanTranslatorBundle_settings_translations_edit")
     * @Method({"GET", "POST"})
     * @Template("KunstmaanTranslatorBundle:General:editTranslation.html.twig")
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();

        $translation = $em->getRepository('KunstmaanTranslatorBundle:Translation')->find($id);
        $form = $this->createForm(new TranslationAdminType(), $translation);

        if ('POST' == $request->getMethod()) {
            $form->bind($request);

            if ($form->isValid()) {

                $em->persist($translation);
                $em->flush();

                $this->get('session')->getFlashBag()->add('success', $this->get('translator')->trans('settings.translator.succesful_added'));

                return new RedirectResponse($this->generateUrl("KunstmaanTranslatorBundle_settings_translations"));
            }
        }

        return array(
            'form' => $form->createView(),
            'translation' => $translation
        );
    }

    /**
     * @param $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws NotFoundHttpException
     * @Route("/{id}/delete", requirements={"id" = "\d+"}, name="KunstmaanTranslatorBundle_settings_translations_delete")
     * @Method({"GET", "POST"})
     */
    public function deleteAction($id)
    {
        /* @var $em EntityManager */
        $em = $this->getDoctrine()->getManager();
        $translation = $em->getRepository('KunstmaanTranslatorBundle:Translation')->findOneById($id);

        $em->remove($translation);
        $em->flush();

        return new RedirectResponse($this->generateUrl("KunstmaanTranslatorBundle_settings_translations"));
    }
}
