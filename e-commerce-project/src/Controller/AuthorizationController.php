<?php
namespace App\Controller;

use App\Exception\UnauthorizedException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class AuthorizationController extends AbstractController
{
    public function extractTokenFromAuthorizationHeader(Request $request, LoggerInterface $logger) : string
    {
        $authorization = $request->headers->get('Authorization');
        if($authorization == "")
        {
            $logger->error("Veuillez vous connecté");
            throw new UnauthorizedException("Vous n'êtes pas connecté, veuillez vous connecter");
        }
        else{
            $token = substr($authorization, strlen("Bearer "), strlen($authorization));
            if($token == "" || $token == null)
            {
                $logger->error("Votre token n'est pas valide");
                throw new UnauthorizedException("Votre token n'est pas valide");
            }
            else
            {
                return $token;
            }
        }
    }
}

?>