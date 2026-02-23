### Question 1
Quels sont selon vous tous éléments de contexte pouvant être pris en considération pour permettre le "dynamisme" d'un site web ?

il y en a 6
- le client
- l'utilisateur
- couche intermédiaire (proxy/reverse proxy)
- backend & api
- 

### Question 2
On constate un changement d'affichage car le code a changé. "Dynamique". Mais surtout, **qu'y a-t-il d'intéressant** dans cette variable affichée ?

Relevez notemment `REQUEST_METHOD` puis soumettez le formulaire avec n'importe quels identifiants, et revérifiez sa valeur : **la valeur a changé, pourquoi ?**

- passe de get à post

### Question 3
Qu'est-ce qui différencie une "page" d'une "URL" ? Est-ce la même chose ?

- url = adresse de la page
- 

### Question 4
Qu'est-ce qui différencie **session** et **authentification** ? Qu'est-ce qui les lie ?

- L'auth = montre qui tu es, session = période dont le système ce souvient de toi
- L'authentification crée la session

### Question 5
Quel code de statut HTTP ([voir la doc](https://developer.mozilla.org/fr/docs/Web/HTTP/Status)) serait pertinent pour ce cas d'erreur ?

### Question 6
Quels sont **tous** les éléments qui mériteraient d'être factorisés entre les deux pages sur lesquelles nous avons travaillé ?

### Question 7
PHP propose 4 expressions différentes pour faire appel à un script depuis un autre. Selon vous, laquelle serait la plus pertinente ici ?

  * `include` ([voir documentation](https://www.php.net/manual/fr/function.include.php))
  * `include_once` ([voir documentation](https://www.php.net/manual/fr/function.include-once.php))
  * `require` ([voir documentation](https://www.php.net/manual/fr/function.require.php))
  * `require_once` ([voir documentation](https://www.php.net/manual/fr/function.require-once.php))


### Question 8
Renseignez-vous : quels sont les **avantages apportés par un SEP** ? Nous savons déjà que c'est une approche facilitant la factorisation du code.

### Question 9
Qu'est-ce qu'une route ? Qu'est-ce qui la constitue ? *Spoiler* : ce n'est pas **juste** une URL !

### Question 10
En l'absence de SEP dans notre code (comme lorsqu'on faisait des appels directs à `login.php` et `dashboard.php`), qui s'occupe de jouer le rôle de SEP et de routage ?

### Question 11
Si vous n'avez créé que 2 routes, associées aux 2 méthodes de contrôleur, la soumission du formulaire ne devrait plus fonctionner correctement : vérifiez.

Pourquoi ? Comment faudrait-il gérer ce cas ?

### Question 12
Où peut-on placer `session_start()` pour le factoriser et s'assurer de ne l'écrire qu'une seule fois ?

### Question 13
Pourquoi est-ce considéré comme une **mauvaise pratique** de réunir au même endroit le code s'occupant du travail métier et le code s'occupant de l'affichage ?

### Question 14
Est-il malgré tout encore possible d'écrire du code métier dans nos *templates*/Vues ?

### Question 15
Twig autorise n'importe quelle extension pour les *templates*. Pourquoi alors utiliser `file.html.twig` et pas quelque-chose de plus simple comme `file.tpl` ?

### Question 16
Dans nos vues, quelles parties du code mériteraient d'être factorisées ?

### Question 17
Quelles sont les autres inconvénients potentiels à utiliser une telle technique pour la structure d'une page web ?

### Question 18
Quel est le changement majeur en termes de responsabilités quand on passe du rendu côté serveur (SSR) au rendu côté client (CSR) ?

### Question 19
Vous avez à ce stade une idée de ce qu'est REST. Mais *pourquoi* utiliser une "architecture" comme celle-là ? Qu'architecture-t-on, quand on fait du REST ?

### Question 20
Comment concilier la contrainte *stateless* de REST avec :

  * la gestion d'authentification (n'implique-t-elle pas de session ?) ;
  * la conservation de données métier côté serveur (BDD, fichiers, etc.).


### Question 21
Pourquoi les conventions tournent autour de l'usage de seulement 5 méthodes HTTP. Pourquoi pas les 4 autres ?

### Question 22
Côté métier : quelles classes vous faudra-il pour travailler sur ces 3 entités ?

### Question 23
Si le modèle enregistre les données en JSON dans des fichiers, et si les *endpoints* communiquent du JSON en HTTP, pourquoi faudrait-il décoder ce JSON pour le réencoder ensuite ?

Ne pourrions-nous pas transmettre directement le JSON du modèle vers la réponse HTTP, ou de la requête HTTP vers le modèle ?

### Question 24
Maintenant que ça marche, prenez le temps de vous renseigner : pourquoi faut-il faire deux appels à `.then()` lors d'un `fetch()` ?

### Question 25
Regardez à nouveau l'onglet "**Réseau**" et constatez l'appel à la page du *front* et celui à l'API. Quelles en-têtes nous transmet l'API ?

### Question 26
Quels sont les avantages et inconvénients à appliquer mécaniquement le CRUD quand on développe une API REST ?

### Question 27
Prenons l'action "Améliorer le bâtiment" (*Upgrade*). Si on utilise uniquement les *endpoints* CRUD créés précédemment, quelles requêtes le client (JS) doit-il envoyer au serveur ?

Quel est le risque majeur de cette approche ?

### Question 28
Quelles sont toutes les méthodes qui permettre de résoudre cette erreur ?

### Question 29
Pourquoi utiliser `Access-Control-Allow-Origin: *` est généralement considéré comme une mauvaise pratique ?

### Question 30
En cas d'erreur, l'utilisation d'une redirection fait "perdre" le message d'erreur à afficher. Pourquoi ?

Quelle(s) solution(s) pour résoudre ce problème ?

