<?php

namespace App\Utility;

use Hateoas\HateoasBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Trait Utils
 *
 * @package App\Controller
 */
trait Utils
{
    /**
     * Generates a response object with the message and corresponding code
     * (serialized according to $format)
     *
     * @param int $code HTTP status
     * @param null|array|object $messageBody HTTP body message
     * @param null|string $format Default JSON
     * @param null|array $headers
     * @return Response Response object
     */
    public static function apiResponse(
        int $code,
        $messageBody = null,
        ?string $format = 'json',
        ?array $headers = null
    ): Response
    {
        if (null === $messageBody) {
            $data = null;
        } else {
            $hateoas = HateoasBuilder::create()->build();
            $data = $hateoas->serialize($messageBody, $format);
        }

        $response = new Response($data, $code);
        $response->headers->add([
            'Access-Control-Allow-Origin' => '*',   // enable CORS
            'Access-Control-Allow-Credentials' => 'true', // Ajax CORS requests with Authorization header
        ]);
        if (!empty($headers)) {
            $response->headers->add($headers);
        }
        switch ($format) {
            case 'xml':
                $response->headers->set('Content-Type', 'application/xml');
                break;
            default:
                $response->headers->set('Content-Type', 'application/json');
        }

        return $response;
    }

    /**
     * Return the request format (xml | json)
     *
     * @param  Request $request
     * @return string (xml | json)
     */
    public static function getFormat(Request $request): string
    {
        $acceptHeader = $request->getAcceptableContentTypes();
        $miFormato = ('application/xml' === $acceptHeader[0])
            ? 'xml'
            : 'json';

        return empty($request->get('_format'))
            ? $miFormato
            : $request->get('_format');
    }
}
