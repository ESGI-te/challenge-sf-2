<?php

namespace App\Controller;

use App\Repository\PlanRepository;
use App\Repository\UserRepository;
use App\Service\PaymentService;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[Route('/payment',name: 'stripe_')]
class PaymentController extends AbstractController
{

    private PaymentService $paymentService;

    public function __construct(Security $security, UserRepository $userRepository,PlanRepository $planRepository)
    {
        $this->paymentService = new PaymentService($security,$userRepository) ;
        $this->planRepository = $planRepository;
    }

    #[Route('/', name: 'payment')]
    public function index(): Response
    {
        $plans = $this->planRepository->findAll();
        return $this->render('payment/index.html.twig', [
            'plans' => $plans,
        ]);
    }

    /**
     * @throws ApiErrorException
     */
    #[Route('/checkout/', name: 'checkout')]
    public function checkout($stripeSK,SessionInterface $session,PlanRepository $planRepository,Request $request): Response
    {
        $plan_id = $request->query->get("plan_id");
        $plan = $planRepository->findOneBy(["id"=>$plan_id]);
        $slug = $this->paymentService->getInstance()->getToken();
        $session->set('payment_slug', $this->paymentService->encryptToken($slug));
        $session->set('plan_id',$plan_id);
        $stripe = new StripeClient($stripeSK);
        $sucessURL = $this->generateUrl('stripe_success',['slug' => $this->paymentService->encryptToken($slug)],UrlGeneratorInterface::ABSOLUTE_URL);
        $cancelURL = $this->generateUrl('stripe_cancel',[],UrlGeneratorInterface::ABSOLUTE_URL);

        return $this->redirect($this->paymentService->paymentCheckout($stripe,$sucessURL,$cancelURL,$plan)->url,303);
    }

    #[Route('/success/{slug}', name: 'success')]
    public function success(UserRepository $userRepository,SessionInterface $session,PlanRepository $planRepository): Response
    {
        $premium_plan = $this->planRepository->findOneBy(['name'=>'Premium']);

        $plan_id = $session->get("plan_id");
        $plan = $planRepository->findOneBy(["id"=>$plan_id]);

        $encryptedSlug = $session->get('payment_slug');
        $slug = $this->paymentService->getInstance()->getToken();
        $decryptedSlug = hash('sha256',  $slug);

        $validSlug = $decryptedSlug === $encryptedSlug;
        if (!$validSlug){
            return $this->render('payment/cancel.html.twig', []);
        }
        $this->paymentService->addTokes($plan->getNbRecipe());
        $userRepository->save($this->paymentService->getInstance(),true);
        return $this->render('payment/success.html.twig', []);
    }

    #[Route('/cancel', name: 'cancel')]
    public function cancel(): Response
    {
        return $this->render('payment/cancel.html.twig', []);
    }

    #[Route('/max_recipe', name: 'max')]
    public function max(): Response
    {
        return $this->render('payment/max_recipe.html.twig', []);
    }
}

