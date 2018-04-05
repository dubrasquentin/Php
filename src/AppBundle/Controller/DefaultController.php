<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use AppBundle\Form\CodeBarreType;
use AppBundle\Form\EvaluationType;
use AppBundle\Entity\Produit;
use AppBundle\Entity\Evaluation;
use AppBundle\Entity\User;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @Template("index.html.twig")
     */
    public function indexAction(Request $request)
    {
        $form = $this->createForm(CodeBarreType::class);
        //Liste des meilleurs produits
        $BestP = $this->getDoctrine()
            ->getRepository(Produit::class)
            ->findTheBest();
        $clProduits =[];
        $i=0;
        foreach($BestP as $produit) {
            $useproduit = $this->getDoctrine()
                ->getRepository(Produit::class)
                ->find($produit['produit_id']);
            $url = 'https://fr.openfoodfacts.org/api/v0/produit/' . $useproduit->getCodeBarre() . '.json';
            $data = json_decode(file_get_contents($url), true);
            array_push($clProduits, array('nom' => $data['product']['product_name'], 'img' => $data['product']['image_front_small_url']));
        $i++;
        }

        //Liste des derniers produits consultés
        $Lastproduit = $this->getDoctrine()
            ->getRepository(Produit::class)
            ->findTheLast();

        $Iproduits =[];
        foreach($Lastproduit as $produit) {
            $url = 'https://fr.openfoodfacts.org/api/v0/produit/' . $produit->getCodeBarre() . '.json';
            $data = json_decode(file_get_contents($url), true);
            array_push($Iproduits, array('nom' => $data['product']['product_name'], 'img' => $data['product']['image_front_small_url']));
        }
        return [
            'form' => $form->createView(),
            'Best' => $clProduits,
            'Last' => $Iproduits

        ];
    }

    /**
     * @Route("/search", name="search")
     * @Template("search.html.twig")
     */
    public function searchAction(Request $request)
    {
        $form = $this->createForm(CodeBarreType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $code_barre = $data['code_barre'];

            // XXX: A faire, chercher si le produit existe, le créer en
            // base et rediriger le visiteur vers la fiche produit
            $url = 'https://fr.openfoodfacts.org/api/v0/produit/'.$code_barre.'.json';
            $data = json_decode(file_get_contents($url), true);
            //echo $data['product']['product_name']."\n";


            if(strlen($code_barre)==13){
                $em = $this->getDoctrine()->getManager();
                $date = new \DateTime(date("Y-m-d H:i:s"));
                $produit = $this->getDoctrine()
                    ->getRepository(Produit::class)
                    ->findByCodeBarre($code_barre);
                if(!$produit) {
                    if ($data['status'] == 1) {
                        $produit = new produit;
                        if ($data['product']['product_name'] != null) { // Check si l'API retourne un produit
                            $produit->setCodeBarre($code_barre);
                            $produit->setNbConsultations(1);
                            $produit->setDateDerniereVue($date);

                            $em->persist($produit);
                            $em->flush();
                        } else {
                            $null = true;
                        }
                    }
                }
                    else{
                        $nbConsultation = $produit[0]->getNbConsultations() + 1;
                        $produit[0]->setNbConsultations($nbConsultation);
                        $produit[0]->setDateDerniereVue($date);

                        $em->flush();
                        $Idproduit=$produit[0]->getId();

                        return[
                            'nom' => $data['product']['product_name'],
                            'img' => $data['product']['image_front_small_url'],
                            'Id'=>$Idproduit
                        ];
                    }
            }

            return [
                'code_barre' => $code_barre
            ];
        } else {
            return $this->redirectToRoute('homepage');
        }
    }

    /**
     * @Route("/product/{id}", name="product")
     * @Template("product.html.twig")
     */
    public function productAction($id, Request $request)
    {
        //recup Commentaire
        $AllEval = $this->getDoctrine()
            ->getRepository(Evaluation::class)
            ->findByIdProduit($id);

        //recup Note
        $noteAvg = $this->getDoctrine()
            ->getRepository(Produit::class)
            ->findAVG($id);

        //recupUser
        $user = $this->getUser();
        $produit = $this->getDoctrine()
            ->getRepository(Produit::class)->find($id);

        //Formulaire évaluation
        $formEval= $this->createForm(EvaluationType::class);
        $formEval->handleRequest($request);

        if ($formEval->isSubmitted() && $formEval->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $dataForm = $formEval->getData();

            $eval = new Evaluation();

            $eval->setCommentaire($dataForm['commentaire']);
            $eval->setNote($dataForm['note']);
            $eval->setProduit($produit);

            $eval->setUser($user);

            $em->persist($eval);
            $em->flush();
        }

        $url = 'https://fr.openfoodfacts.org/api/v0/produit/'. $produit->getCodeBarre() .'.json';
        $data = json_decode(file_get_contents($url), true);
        return [
            'evals' =>$AllEval,
            'note' => $noteAvg,
            'nom' => $data['product']['product_name'],
            'img' => $data['product']['image_front_small_url'],
            'code_barre' => $produit->getCodeBarre(),
            'quantity' => $data['product']['quantity'],
            'nbConsultation' => $produit->getNbConsultations(),
            'form' => $formEval->createView()
        ];
    }
}
