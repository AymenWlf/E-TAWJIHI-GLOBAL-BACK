<?php

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\ResponseEvent;

class CorsListener
{
    public function onKernelResponse(ResponseEvent $event)
    {
        $response = $event->getResponse();
        $request = $event->getRequest();

        // Vérifier si c'est une requête CORS
        if ($request->headers->has('Origin')) {
            $origin = $request->headers->get('Origin');

            // Autoriser les origines locales et de production
            $allowedOrigins = [
                'http://localhost:3000',
                'http://127.0.0.1:3000',
                'http://localhost:5173', // Vite dev server
                'http://127.0.0.1:5173',
                'https://e-tawjihi.ma',
                'https://www.e-tawjihi.ma',
                'https://e-tawjihi.com',
                'https://www.e-tawjihi.com',
                'http://e-tawjihi.ma', // Pour les tests en HTTP
                'http://www.e-tawjihi.ma',
                'http://e-tawjihi.com',
                'http://www.e-tawjihi.com'
            ];

            if (in_array($origin, $allowedOrigins)) {
                $response->headers->set('Access-Control-Allow-Origin', $origin);
            }
        }

        // Headers CORS
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS, PATCH');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, Accept');
        $response->headers->set('Access-Control-Allow-Credentials', 'true');
        $response->headers->set('Access-Control-Max-Age', '3600');

        // Gérer les requêtes OPTIONS (preflight)
        if ($request->getMethod() === 'OPTIONS') {
            $response->setStatusCode(200);
            $response->setContent('');
        }
    }
}
