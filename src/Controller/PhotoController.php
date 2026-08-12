<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Photo;
use App\Repository\PhotoRepository;

final class PhotoController extends AbstractController
{
    #[Route('/photo', name: 'app_photo')]
    public function index(PhotoRepository $photoRepository): Response
    {
        $photos = $photoRepository->findAll();
        return $this->render('photo/index.html.twig', [
            'photos' => $photos,
        ]);
    }

    #[Route('/photo/new', name: 'photo_new')]
    public function new(EntityManagerInterface $entityManager, Request $request): Response
    {
        $photo = new Photo();
        $photo->setDateTime(new \DateTime());

        $form = $this->createFormBuilder($photo)
            ->add('image', FileType::class, [
                'mapped' => false,
                'required' => true,
            ])
            ->add('appartenance', TextType::class, [
                'attr' => [
                    'placeholder' => 'Enter name'
                ]
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Post photo'
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();

            if ($imageFile instanceof UploadedFile) {
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();

                $imageFile->move(
                    $this->getParameter('photo_directory'),
                    $newFilename
                );

                $photo->setImage($newFilename);
            }

            $entityManager->persist($photo);
            $entityManager->flush();
            return $this->redirectToRoute('app_photo');
        }

        return $this->render('photo/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/photo/{id}', name: 'photo_show')]
    public function show(EntityManagerInterface $entityManager, int $id): Response
    {
        $photo = $entityManager->getRepository(Photo::class)->find($id);

        if (!$photo) {
            throw $this->createNotFoundException(
                'No photo found for id '.$id
            );
        }

        return new Response('Check out this great product: '.$product->getName());

        // or render a template
        // in the template, print things with {{ product.name }}
        // return $this->render('product/show.html.twig', ['product' => $product]);
    }
}
