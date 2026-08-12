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
use Symfony\Component\Validator\Constraints\File;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Photo;
use App\Repository\PhotoRepository;

final class PhotoController extends AbstractController
{
    #[Route('/', name: 'app_photo')]
    public function index(EntityManagerInterface $entityManager, Request $request, PhotoRepository $photoRepository): Response
    {
        $photos = $photoRepository->findAll();

        $photosByDayAndHour = [];

        foreach ($photos as $photo) {
            $dateTime = $photo->getDateTime();

            $day = $dateTime->format('d/m/Y');
            $hour = $dateTime->format('H');

            $photosByDayAndHour[$day][$hour][] = $photo;
        }

        krsort($photosByDayAndHour);
        foreach ($photosByDayAndHour as $day => &$hours) {
            krsort($hours);
            foreach ($hours as $hour => &$hourPhotos) {
                usort($hourPhotos, fn($a, $b) => $b->getDateTime() <=> $a->getDateTime());
            }
            unset($hourPhotos);
        }
        unset($hours);

        $photo = new Photo();
        $photo->setDateTime(new \DateTime());

        $form = $this->createFormBuilder($photo)
            ->add('Photo', FileType::class, [
                'label' => "Ajouter une photo",
                'mapped' => false,
                'required' => true,
                'constraints' => [
                    new File(
                        maxSize: '20M',
                        mimeTypes: [
                            'image/jpeg',
                            'image/png',
                            'image/gif',
                            'image/heic',
                            'image/heif',
                            'image/heic-sequence',
                            'image/heif-sequence',
                        ],
                        mimeTypesMessage: 'Please upload a valid image (JPEG, PNG, GIF, or HEIC).',
                    ),
                ],
            ])
            ->add('appartenance', TextType::class, [
                'attr' => [
                    'required'   => true,
                    
                ],
                'label' => ' ',
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Ajoute la photo'
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('Photo')->getData();

            if ($imageFile instanceof UploadedFile) {
                $originalExtension = strtolower($imageFile->guessExtension());
                $newFilename = uniqid();

                if (in_array($originalExtension, ['heic', 'heif'])) {
                    $imagick = new \Imagick($imageFile->getPathname());
                    $imagick->setImageFormat('jpeg');
                    $imagick->writeImage($this->getParameter('photo_directory') . '/' . $newFilename . '.jpg');
                    $newFilename .= '.jpg';
                } else {
                    $newFilename .= '.' . $originalExtension;
                    $imageFile->move($this->getParameter('photo_directory'), $newFilename);
                }

                $photo->setImage($newFilename);
            }

            $entityManager->persist($photo);
            $entityManager->flush();
            return $this->redirectToRoute('app_photo');
        }

        return $this->render('photo/index.html.twig', [
            'photosByDayAndHour' => $photosByDayAndHour,
            'form' => $form->createView(),
        ]);
    }
}
