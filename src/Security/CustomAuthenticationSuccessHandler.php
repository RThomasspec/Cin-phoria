<?php
namespace App\Security;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

class CustomAuthenticationSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    private $jwtManager;

    public function __construct(JWTTokenManagerInterface $jwtManager)
    {
        $this->jwtManager = $jwtManager;
    }


    //le TokenInterface c'est un objet qui contient des informations sur l'utilisateur, comme son identité et ses rôles, mais ce n'est pas un JWT.
    public function onAuthenticationSuccess(Request $request, TokenInterface $token ): JsonResponse
    {
        /** @var \App\Entity\Utilisateur $user */
        $user = $token->getUser();

        $jwt = $this->jwtManager->create($user);

        $data = [
            'token' => $jwt,
            'utilisateur_id' => $user->getId(),
            'email_utilisateur' => $user->getMail(),
           
        ];

        return new JsonResponse($data);
    }
}
