# Plan d'implémentation : Base de connaissances et priorisation pour E-DVISOR

## Objectif

Créer une base de connaissances structurée pour améliorer les réponses de l'agent E-DVISOR et permettre la priorisation de l'affichage des programmes et établissements.

---

## 📋 Structure à créer

### 1. Entité `KnowledgeBaseArticle`

Stocke les articles de procédures, FAQ, et connaissances pour l'agent.

**Champs :**

- `id` (int, auto)
- `title` (string, 255) - Titre de l'article
- `category` (string, 50) - Catégorie : 'procedure', 'faq', 'country_guide', 'service_info', etc.
- `content` (text) - Contenu de l'article en français
- `contentEn` (text, nullable) - Contenu en anglais
- `keywords` (json) - Mots-clés pour la recherche sémantique
- `country` (string, nullable) - Pays concerné (France, China, Morocco, etc.)
- `priority` (int, default: 0) - Priorité d'affichage (plus élevé = plus important)
- `isActive` (boolean, default: true)
- `createdAt` (datetime)
- `updatedAt` (datetime)

**Exemples d'articles :**

- Procédure Campus France
- Procédure Parcoursup
- Procédure CSC Chine
- FAQ budget minimum France
- Guide écoles publiques Maroc

### 2. Entité `EAdvisorPriority`

Priorise l'affichage de programmes/établissements spécifiques.

**Champs :**

- `id` (int, auto)
- `type` (string, 20) - 'program' ou 'establishment'
- `entityId` (int) - ID du programme ou établissement
- `priority` (int, default: 0) - Score de priorité (plus élevé = affiché en premier)
- `searchKeywords` (json) - Mots-clés qui déclenchent cette priorité
- `country` (string, nullable) - Pays concerné
- `subject` (string, nullable) - Domaine d'étude
- `studyLevel` (string, nullable) - Niveau d'étude
- `isActive` (boolean, default: true)
- `validFrom` (datetime, nullable)
- `validUntil` (datetime, nullable)
- `createdAt` (datetime)
- `updatedAt` (datetime)

**Exemples :**

- Program ID 123 → Priorité 100 quand recherche "informatique" + "France"
- Establishment ID 45 → Priorité 80 quand recherche "Maroc" + "ingénierie"

### 3. Repository `KnowledgeBaseArticleRepository`

- `findActiveByCategory(string $category): array`
- `findByKeywords(array $keywords): array`
- `findByCountry(string $country): array`
- `searchArticles(string $query): array`

### 4. Repository `EAdvisorPriorityRepository`

- `findPrioritiesForSearch(array $filters): array`
- `getPriorityScore(int $entityId, string $type, array $searchContext): int`

### 5. Service `EAdvisorKnowledgeService`

Service central pour gérer la KB et les priorités.

**Méthodes :**

- `getRelevantArticles(array $context): array` - Récupère les articles pertinents selon le contexte
- `getKnowledgeForPrompt(array $context): string` - Formate les connaissances pour le prompt Grok
- `calculatePriorities(array $entities, array $filters): array` - Calcule les priorités et trie les entités

### 6. Modification de `GrokService`

Intégrer la base de connaissances dans le prompt système.

```php
private function buildSystemPrompt(array $context): string
{
    $basePrompt = "..."; // Prompt actuel

    // Ajouter connaissances pertinentes
    $knowledge = $this->knowledgeService->getKnowledgeForPrompt($context);
    $basePrompt .= "\n\n=== BASE DE CONNAISSANCES ===\n" . $knowledge;

    return $basePrompt;
}
```

### 7. Modification des Repositories de recherche

Modifier `EstablishmentRepository` et `ProgramRepository` pour intégrer les priorités.

```php
public function findByFiltersWithPriority(array $filters, int $limit, int $offset): array
{
    $results = $this->findByFilters($filters, $limit * 2, $offset); // Récupérer plus de résultats

    // Appliquer les priorités
    $priorities = $this->priorityRepository->findPrioritiesForSearch($filters);
    $scored = $this->scoreEntities($results, $priorities);

    // Trier par score de priorité décroissant
    usort($scored, fn($a, $b) => $b['priority_score'] <=> $a['priority_score']);

    return array_slice($scored, 0, $limit);
}
```

### 8. Endpoints API Admin

Créer `AdminKnowledgeBaseController` pour gérer la KB et les priorités.

**Routes :**

- `POST /api/admin/knowledge-base/articles` - Créer un article
- `PUT /api/admin/knowledge-base/articles/{id}` - Modifier un article
- `DELETE /api/admin/knowledge-base/articles/{id}` - Supprimer un article
- `GET /api/admin/knowledge-base/articles` - Lister les articles
- `POST /api/admin/eadvisor/priorities` - Créer une priorité
- `PUT /api/admin/eadvisor/priorities/{id}` - Modifier une priorité
- `DELETE /api/admin/eadvisor/priorities/{id}` - Supprimer une priorité
- `GET /api/admin/eadvisor/priorities` - Lister les priorités

---

## 🔄 Flux d'utilisation

### Lors d'une question utilisateur :

1. **Récupération du contexte** : pays, domaine, niveau d'étude mentionnés
2. **Recherche d'articles pertinents** : articles KB correspondant au contexte
3. **Injection dans le prompt** : articles ajoutés au prompt système de Grok
4. **Grok répond** avec les connaissances de la KB

### Lors d'une recherche d'établissements/programmes :

1. **Récupération des résultats** : recherche normale avec filtres
2. **Calcul des priorités** : pour chaque résultat, calcul du score de priorité
3. **Tri par priorité** : résultats triés par score décroissant
4. **Affichage** : les résultats prioritaires apparaissent en premier

---

## 📝 Exemple de données

### KnowledgeBaseArticle

```json
{
  "title": "Procédure Campus France",
  "category": "procedure",
  "content": "Pour étudier en France, tu dois :\n1. Créer un compte Campus France\n2. Préparer tes documents...",
  "keywords": ["campus france", "france", "procédure", "visa"],
  "country": "France",
  "priority": 100
}
```

### EAdvisorPriority

```json
{
  "type": "program",
  "entityId": 123,
  "priority": 100,
  "searchKeywords": ["informatique", "master", "france"],
  "country": "France",
  "subject": "Computer Science",
  "studyLevel": "Master"
}
```

---

## 🎯 Priorisation logique

**Score de priorité calculé comme suit :**

- Score de base de l'entité (si définie) : 0-100
- Bonus mots-clés match : +20 par mot-clé correspondant
- Bonus pays match : +30 si pays correspond
- Bonus domaine match : +25 si domaine correspond
- Bonus niveau match : +15 si niveau correspond

**Résultat final** : Les entités avec le score le plus élevé apparaissent en premier.

---

## ✅ Étapes d'implémentation

1. ✅ Créer les entités `KnowledgeBaseArticle` et `EAdvisorPriority`
2. ✅ Créer les migrations Doctrine
3. ✅ Créer les Repositories
4. ✅ Créer le Service `EAdvisorKnowledgeService`
5. ✅ Modifier `GrokService` pour injecter la KB
6. ✅ Modifier les Repositories de recherche pour intégrer les priorités
7. ✅ Créer les endpoints API Admin
8. ✅ Créer une interface admin (optionnel)

---

## 🔧 Configuration nécessaire

### Variables d'environnement

Aucune nouvelle variable nécessaire.

### Permissions

- `ROLE_ADMIN` pour gérer la KB et les priorités

---

## 📊 Impact attendu

1. **Réponses plus précises** : L'agent aura accès aux procédures et informations officielles
2. **Priorisation personnalisée** : Vous contrôlez quels programmes/écoles apparaissent en premier
3. **Maintenance facile** : Base de connaissances modifiable via API/admin
4. **Évolutivité** : Facile d'ajouter de nouveaux articles et priorités
