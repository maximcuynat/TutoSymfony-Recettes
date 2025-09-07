<?php

namespace App\Controller;

use App\DTO\ContactDTO;
use App\Form\ContactType;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class ContactController extends AbstractController
{
    #[Route('/contact', name: 'contact')]
    public function contact(Request $request, MailerInterface $mailer): Response
    {
        $data = new ContactDTO();

        // TODO : Supprimer ca 
        $data->name = 'Joth Doe';
        $data->email = 'john@doe.fr';
        $data->message = 'Super site internet !';


        $form = $this->createForm(ContactType::class, $data);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $mail = (new TemplatedEmail())
                    ->to($data->email)
                    ->from($data->email)
                    ->subject('Demande de contact')
                    ->htmlTemplate('emails/contact.html.twig')
                    ->context(['data' => $data]);
                $mailer->send($mail);
            } catch (\Exception $e) {
                $this->addFlash('danger', 'Impossible d\'envoyer votre email !');
            }
            $this->addFlash('success', "L'email à bien été envoyé.");
            return $this->redirectToRoute('contact');
        }
        return $this->render('contact/contact.html.twig', [
            'controller_name' => 'ContactController',
            'form' => $form
        ]);
    }
}
