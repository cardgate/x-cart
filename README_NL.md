![CardGate](https://cdn.curopayments.net/thumb/200/logos/cardgate.png)

# CardGate module voor X-Cart

## Support

Deze module is geschikt voor X-Cart **4.4.0** tot **4.7.x** .

## Voorbereiding

Voor het gebruik van deze module zijn CardGate inloggegevens noodzakelijk.

Ga a.u.b. naar [Mijn CardGate](https://my.cardgate.com/) en kopieer de  site ID en hash key,  
of vraag deze gegevens aan uw accountmanager.

## Installatie

1. Download en unzip de meest recente [source code](https://github.com/cardgate/x-cart/releases/) op je bureaublad.

2. Upload de **install_cardgateplus, include** en **payment** mappen naar de **root** map van je webshop.

3. Open **http://mijnwebshop.com/install_cardgateplus** in je browser en installeer de module.  
   (Vervang **http://mijnwebshop.com** met de URL van jouw webshop).
    
4. Verwijder uit veiligheidsoverwegingen de **install_cardgateplus** map van je webshop.

## Configuratie

1. Login op het **admin** gedeelte van je webshop.

2. Klik op de **Settings** tab en kies **Payment methods**.

3. Klik op de **Configure** link van de **betaalmethode** die je wenst te activeren.

4. Vul hier de **site ID** en de **hash key** in, deze kun je vinden bij **Sites** op [Mijn CardGate](https://my.cardgate.com/).

5. Selecteer de **valuta** die je wenst te gebruiken.

6. Vul de standaard **gateway taal** in.

7. Indien je gebruik wenst te maken van de log optie, controleer dan dat de log map van je website **schrijfprivileges** heeft.  
   Bijvoorbeeld de map **http://mijnwebshop.com/payment/cardgateplus/logs** .

8. Klik op de **Update** knop en sla de instellingen op.

9. Klik opnieuw op de **Settings** tab en klik op **Betaalmethoden**.

10. Selecteer de betaalmethode die je hebt ingesteld en verander indien gewenst de naam van de betaalmethode en de speciale instructies.  
    Klik nu op **Apply Changes**.

11. Herhaal de **stappen 3 tot en met 10** voor **iedere betaalmethode** die je wenst te **activeren**.

12. Ga naar [Mijn CardGate](https://my.cardgate.com/), kies **Sites** en selecteer de juiste site.

13. Vul bij **Technische koppeling** de **Callback URL** in:  
    De Callback URL wordt in iedere betaalmodule getoond en kan direct gekopieerd worden.  
    
14. Indien je voor meerdere betaalmethoden **dezelfde** site ID en hash key gebruikt,  
    kan je de Callback URL gebruiken van een **willekeurige** CardGate betaalmethode, zolang hij maar actief is.  
    Deze Callback zal ook de Callbacks naar andere CardGate betaalmethoden op de juiste manier verwerken.  

15. Verwijder uit veiligheidsoverwegingen de **install_cardgateplus** map van je webshop.

16. Zorg ervoor dat je na het testen **alle geactiveerde betaalmethoden** omschakelt van **Test mode** naar **Live mode** en sla het op (**Save**).

## Vereisten

Geen verdere vereisten.
