# Paiement sur place lors du retrait (OnPickupPayment)

Module de paiement Thelia 2 permettant de proposer l'option **paiement sur place lors du retrait** : le règlement est encaissé au comptoir au moment où le client retire sa commande.

## Compatibilité

- Thelia >= 2.5.5
- PHP >= 8.3

## Installation

### Via Composer

```bash
composer require anselmi/on-pickup-payment-module
```

### Manuel

Copier le dossier `OnPickupPayment` dans `local/modules/` de votre installation Thelia.

### Activation

Depuis le back-office Thelia : **Modules → Paiement sur place lors du retrait → Activer**.

À l'activation, le module crée automatiquement un statut de commande dédié :

| Code | Libellé | Couleur |
|---|---|---|
| `on_pickup_payment_paid` | Paiement sur place lors du retrait | `#14b8a6` |

Ce statut est marqué `protected` afin de ne pas être supprimé accidentellement.

## Fonctionnement

1. À la finalisation de la commande, le client sélectionne « Paiement sur place lors du retrait » comme moyen de paiement.
2. Le module valide la commande sans transaction monétique.
3. Le listener `OrderCreateListener` repositionne automatiquement la commande sur le statut `on_pickup_payment_paid` :
   - sur l'événement `ORDER_PAY` (parcours client classique),
   - sur l'événement `ORDER_CREATE_MANUAL` (création depuis le back-office).
4. La gestion de stock à la création de commande est désactivée (`manageStockOnCreation()` retourne `false`).

## Configuration

Aucune configuration n'est requise. Le module est opérationnel dès son activation.

Vous pouvez restreindre la disponibilité du module via la gestion standard Thelia :

- pays autorisés
- zones de livraison
- montants minimum / maximum

## Désinstallation

Depuis le back-office : **Modules → Paiement sur place lors du retrait → Désactiver**, puis supprimer le dossier `local/modules/OnPickupPayment` si souhaité.

> Le statut de commande `on_pickup_payment_paid` n'est pas supprimé automatiquement pour préserver l'historique des commandes existantes.

## Licence

Proprietary

## Auteur

- mdevaud — <mdevaud@openstudio.fr>
