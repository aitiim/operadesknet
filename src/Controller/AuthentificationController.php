<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class AuthentificationController extends AbstractController
{
    #[Route('/login', name: 'hotel_login')]
    public function loginAction(Request $request, UserRepository $userRepository): Response
    {
        $login = $request->query->get('user');
        $password = $request->query->get('password');

        $user = $userRepository->findOneBy([
            'username' => $login
        ]);

        if ($user !== null && $user->getPassword() === $password) {
            return new Response('success: true');
        } else {
            return new Response('success: false');
        }
    }
}
