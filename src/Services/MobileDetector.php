<?php

namespace Hdaklue\Actioncrumb\Services;

class MobileDetector
{
    protected $agent;

    public function __construct()
    {
        if (class_exists(\Jenssegers\Agent\Agent::class)) {
            $this->agent = new \Jenssegers\Agent\Agent();
        } else {
            $this->agent = null;
        }
    }

    public function isMobile(): bool
    {
        if (!$this->agent) {
            return $this->fallbackIsMobile();
        }
        return $this->agent->isMobile();
    }

    public function isTablet(): bool
    {
        if (!$this->agent) {
            return $this->fallbackIsTablet();
        }
        return $this->agent->isTablet();
    }

    public function isMobileOrTablet(): bool
    {
        return $this->isMobile() || $this->isTablet();
    }

    public function isDesktop(): bool
    {
        return !$this->isMobileOrTablet();
    }

    public function getPlatform(): string
    {
        if ($this->isTablet()) {
            return 'tablet';
        }
        
        if ($this->isMobile()) {
            return 'mobile';
        }

        return 'desktop';
    }

    public function getDeviceType(): string
    {
        if (!$this->agent) {
            return $this->getPlatform();
        }
        return $this->agent->device();
    }

    public function getBrowser(): string
    {
        if (!$this->agent) {
            return 'unknown';
        }
        return $this->agent->browser();
    }

    /**
     * Fallback mobile detection using basic user agent parsing
     */
    protected function fallbackIsMobile(): bool
    {
        $userAgent = request()->userAgent() ?? '';
        
        $mobilePatterns = [
            'Mobile', 'Android', 'iPhone', 'iPad', 'iPod', 'BlackBerry', 
            'Windows Phone', 'Opera Mini', 'IEMobile', 'Mobile Safari'
        ];
        
        foreach ($mobilePatterns as $pattern) {
            if (stripos($userAgent, $pattern) !== false) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Fallback tablet detection using basic user agent parsing
     */
    protected function fallbackIsTablet(): bool
    {
        $userAgent = request()->userAgent() ?? '';
        
        $tabletPatterns = [
            'iPad', 'Android.*Tablet', 'Kindle', 'Silk/', 'Tablet'
        ];
        
        foreach ($tabletPatterns as $pattern) {
            if (preg_match('/' . $pattern . '/i', $userAgent)) {
                return true;
            }
        }
        
        return false;
    }
}