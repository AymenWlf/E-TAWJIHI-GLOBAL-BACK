# Guide d'installation du Test de Diagnostic

## 📋 Étapes d'installation

### 1. Créer les migrations Doctrine

```bash
cd "E-TAWJIHI GLOBAL (BACKEND)"
php bin/console make:migration
php bin/console doctrine:migrations:migrate
```

### 2. Insérer des questions d'exemple

Les questions doivent être insérées dans la table `diagnostic_questions`.

**Structure des catégories recommandées :**
- `academic` : Questions académiques (niveau, filière, notes, etc.)
- `career` : Questions de carrière (objectifs professionnels, intérêts)
- `personality` : Questions de personnalité (traits, préférences de travail)
- `skills` : Questions sur les compétences (langues, techniques, soft skills)
- `preferences` : Questions sur les préférences (pays, type d'établissement, budget)
- `motivation` : Questions de motivation (pourquoi étudier, objectifs)

**Exemple de question SQL :**

```sql
INSERT INTO diagnostic_questions (category, question_text, question_text_en, type, options, order_index, is_active, is_required, created_at, updated_at) VALUES
('academic', 'Quel est ton niveau d''étude actuel ?', 'What is your current level of education?', 'select', 
  JSON_ARRAY(
    JSON_OBJECT('value', 'terminal', 'label', 'Terminale', 'score', 3),
    JSON_OBJECT('value', 'bac', 'label', 'Bac', 'score', 5),
    JSON_OBJECT('value', 'bac1', 'label', 'Bac+1', 'score', 6),
    JSON_OBJECT('value', 'bac2', 'label', 'Bac+2', 'score', 7),
    JSON_OBJECT('value', 'licence', 'label', 'Licence', 'score', 8),
    JSON_OBJECT('value', 'master', 'label', 'Master', 'score', 9)
  ),
  1, 1, 1, NOW(), NOW()
);

INSERT INTO diagnostic_questions (category, question_text, question_text_en, type, options, order_index, is_active, is_required, created_at, updated_at) VALUES
('academic', 'Quelle est ta moyenne générale au baccalauréat ?', 'What is your overall average in the baccalaureate?', 'select',
  JSON_ARRAY(
    JSON_OBJECT('value', 'under_12', 'label', 'Moins de 12/20', 'score', 2),
    JSON_OBJECT('value', '12_14', 'label', '12-14/20', 'score', 5),
    JSON_OBJECT('value', '14_16', 'label', '14-16/20', 'score', 7),
    JSON_OBJECT('value', '16_18', 'label', '16-18/20', 'score', 9),
    JSON_OBJECT('value', 'over_18', 'label', 'Plus de 18/20', 'score', 10)
  ),
  2, 1, 1, NOW(), NOW()
);

INSERT INTO diagnostic_questions (category, question_text, question_text_en, type, options, order_index, is_active, is_required, created_at, updated_at) VALUES
('career', 'Quel domaine professionnel t''intéresse le plus ?', 'Which professional field interests you most?', 'multiselect',
  JSON_ARRAY(
    JSON_OBJECT('value', 'engineering', 'label', 'Ingénierie', 'score', 8),
    JSON_OBJECT('value', 'medicine', 'label', 'Médecine', 'score', 9),
    JSON_OBJECT('value', 'business', 'label', 'Commerce/Gestion', 'score', 7),
    JSON_OBJECT('value', 'it', 'label', 'Informatique', 'score', 8),
    JSON_OBJECT('value', 'law', 'label', 'Droit', 'score', 6),
    JSON_OBJECT('value', 'arts', 'label', 'Arts/Lettres', 'score', 5)
  ),
  1, 1, 0, NOW(), NOW()
);

INSERT INTO diagnostic_questions (category, question_text, question_text_en, type, options, order_index, is_active, is_required, created_at, updated_at) VALUES
('personality', 'Comment te décrirais-tu ?', 'How would you describe yourself?', 'multiselect',
  JSON_ARRAY(
    JSON_OBJECT('value', 'analytical', 'label', 'Analytique', 'score', 7),
    JSON_OBJECT('value', 'creative', 'label', 'Créatif', 'score', 6),
    JSON_OBJECT('value', 'leadership', 'label', 'Leader', 'score', 8),
    JSON_OBJECT('value', 'team_player', 'label', 'Esprit d''équipe', 'score', 7),
    JSON_OBJECT('value', 'independent', 'label', 'Indépendant', 'score', 6),
    JSON_OBJECT('value', 'detail_oriented', 'label', 'Méticuleux', 'score', 7)
  ),
  1, 1, 0, NOW(), NOW()
);

INSERT INTO diagnostic_questions (category, question_text, question_text_en, type, options, order_index, is_active, is_required, created_at, updated_at) VALUES
('skills', 'Évalue ta maîtrise de l''anglais', 'Rate your English proficiency', 'scale',
  JSON_ARRAY(
    JSON_OBJECT('value', '1', 'label', '1', 'score', 1),
    JSON_OBJECT('value', '2', 'label', '2', 'score', 2),
    JSON_OBJECT('value', '3', 'label', '3', 'score', 3),
    JSON_OBJECT('value', '4', 'label', '4', 'score', 4),
    JSON_OBJECT('value', '5', 'label', '5', 'score', 5),
    JSON_OBJECT('value', '6', 'label', '6', 'score', 6),
    JSON_OBJECT('value', '7', 'label', '7', 'score', 7),
    JSON_OBJECT('value', '8', 'label', '8', 'score', 8),
    JSON_OBJECT('value', '9', 'label', '9', 'score', 9),
    JSON_OBJECT('value', '10', 'label', '10', 'score', 10)
  ),
  1, 1, 0, NOW(), NOW()
);

INSERT INTO diagnostic_questions (category, question_text, question_text_en, type, options, order_index, is_active, is_required, created_at, updated_at) VALUES
('preferences', 'Dans quel pays souhaites-tu étudier ?', 'In which country do you want to study?', 'multiselect',
  JSON_ARRAY(
    JSON_OBJECT('value', 'france', 'label', 'France', 'score', 8),
    JSON_OBJECT('value', 'china', 'label', 'Chine', 'score', 7),
    JSON_OBJECT('value', 'morocco', 'label', 'Maroc', 'score', 6),
    JSON_OBJECT('value', 'canada', 'label', 'Canada', 'score', 9),
    JSON_OBJECT('value', 'usa', 'label', 'États-Unis', 'score', 9),
    JSON_OBJECT('value', 'uk', 'label', 'Royaume-Uni', 'score', 8)
  ),
  1, 1, 1, NOW(), NOW()
);

INSERT INTO diagnostic_questions (category, question_text, question_text_en, type, options, order_index, is_active, is_required, created_at, updated_at) VALUES
('motivation', 'Quelle est ta motivation principale pour poursuivre des études ?', 'What is your main motivation for pursuing studies?', 'select',
  JSON_ARRAY(
    JSON_OBJECT('value', 'career', 'label', 'Carrière professionnelle', 'score', 8),
    JSON_OBJECT('value', 'passion', 'label', 'Passion pour le domaine', 'score', 9),
    JSON_OBJECT('value', 'salary', 'label', 'Salaire élevé', 'score', 6),
    JSON_OBJECT('value', 'family', 'label', 'Attentes familiales', 'score', 5),
    JSON_OBJECT('value', 'travel', 'label', 'Voyage/Découverte', 'score', 7),
    JSON_OBJECT('value', 'skills', 'label', 'Développement de compétences', 'score', 8)
  ),
  1, 1, 1, NOW(), NOW()
);
```

### 3. Types de questions supportés

- **select** : Choix unique parmi plusieurs options
- **multiselect** : Choix multiple parmi plusieurs options
- **scale** : Échelle numérique (généralement 1-10)
- **text** : Réponse textuelle libre
- **number** : Nombre entier

### 4. Structure des options

Chaque option doit avoir :
- `value` : Valeur stockée
- `label` : Texte affiché
- `score` : Score associé (1-10) pour le calcul de priorités

### 5. Ajouter la route dans App.tsx

```jsx
import DiagnosticTestPage from './pages/DiagnosticTestPage';

// Dans les Routes :
<Route path="/diagnostic" element={<ProtectedRoute><DiagnosticTestPage /></ProtectedRoute>} />
```

### 6. Exemple de prompt Grok

Le service `DiagnosticService` construit automatiquement un prompt détaillé pour Grok incluant :
- Les scores par catégorie
- Un résumé des réponses clés
- Instructions pour générer un diagnostic complet

Le diagnostic généré inclura :
1. Résumé exécutif
2. Points forts identifiés
3. Axes d'amélioration
4. Orientation recommandée
5. Plan d'action

---

## 🎯 Recommandations

- **Minimum 50 questions** : Réparties sur 5-6 catégories (environ 8-10 questions par catégorie)
- **Mix de types** : Utilisez différents types de questions pour varier l'expérience
- **Scores cohérents** : Les scores doivent être cohérents avec la valeur de la réponse
- **Ordre logique** : Commencez par les questions les plus simples, finissez par les plus complexes

