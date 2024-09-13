<?php

namespace MGS\StoreLocator\Plugin\ReCaptcha\Model;

use MSP\ReCaptcha\Model\LayoutSettings as Subject;
use MGS\StoreLocator\Model\Config;

/**
 * Class LayoutSettings
 * The class responsible for adding custom_form zone to MSP_ReCaptcha Layout setting
 */
class LayoutSettings
{
    /**
     * @var Config
     */
    private $config;

    /**
     * LayoutSettings constructor.
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @param Subject $subject
     * @param array $result
     * @return array
     */
    public function afterGetCaptchaSettings(Subject $subject, array $result)
    {
        if (isset($result['enabled'])) {
            $result['enabled']['custom_form'] = $this->config->isEnabledFrontendCustomForm();
        }
        return $result;
    }
}