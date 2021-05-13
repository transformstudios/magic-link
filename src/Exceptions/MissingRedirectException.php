<?php

namespace TransformStudios\MagicLink\Exceptions;

use Exception;
use Facade\IgnitionContracts\BaseSolution;
use Facade\IgnitionContracts\ProvidesSolution;
use Facade\IgnitionContracts\Solution;

class MissingRedirectException extends Exception implements ProvidesSolution
{
    public function getSolution(): Solution
    {
        $url = '/cp/magic-link/config/edit';
        $description = "It appears that you forgot to set a `redirect` in the configuration. Please go to the [control panel]({$url}) in the link below to set it.";

        return BaseSolution::create('Missing `redirect`')
            ->setSolutionDescription($description)
            ->setDocumentationLinks([
                'Passwordless Login Config' => $url,
            ]);
    }
}
