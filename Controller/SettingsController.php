<?php

namespace UAM\Bundle\SettingsBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SettingsController extends Controller
{
    /**
	 * The action to manipulate application wide settings
	 *
	 * @Route("/settings", name="manage_settings")
	 * @Template()
	 *
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 */
    public function settingsAction(Request $request)
    {
        $form = $this->createForm(new SettingsFormType($this->get('settings')));

        $form->handle($request);

        if ($form->isValid()) {
            $this->get('settings')->setReadOnly(false);

            foreach ($form->getViewData() as $key => $data) {
                $settingKey = str_replace('_', '.', $key);
                $this->get('settings')->set($settingKey, $data);
            }

            $this->get('settings')->saveToFile();
            $this->get('settings')->setReadOnly(true);
            $this->get('session')->getFlashBag()->add('success', 'The settings have been saved');
        }

        return array(
            'form' => $form->createView()
        );
    }
}
