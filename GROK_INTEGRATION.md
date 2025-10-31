# Guide d'intégration Grok API dans E-DVISOR

## 📋 Prérequis

1. **Clé API Grok (xAI)**
   - Créer un compte sur https://x.ai
   - Obtenir une clé API depuis le dashboard xAI
   - Documentation: https://docs.x.ai

2. **Configuration Symfony**
   - Symfony 6+ avec HttpClient configuré
   - Accès au fichier `.env` du backend

## 🔧 Configuration Backend

### 1. Ajouter la clé API dans `.env`

```bash
# .env ou .env.local
GROK_API_KEY=your_grok_api_key_here
```

### 2. Installer les dépendances (si nécessaire)

```bash
composer require symfony/http-client
```

### 3. Services créés

- **`App\Service\GrokService`** : Service pour communiquer avec l'API Grok
- **`App\Controller\EAdvisorController`** : Endpoints API pour E-DVISOR

## 🎯 Endpoints disponibles

### 1. Chat ouvert (`POST /api/eadvisor/chat`)
Répond aux questions libres de l'utilisateur dans la barre de chat.

**Request:**
```json
{
  "question": "Combien coûte étudier en France ?",
  "conversationHistory": [
    {"role": "user", "content": "Bonjour"},
    {"role": "assistant", "content": "Bonjour ! Comment puis-je t'aider ?"}
  ],
  "userProfile": {
    "studyDestination": ["FR"],
    "budget": 8000
  }
}
```

**Response:**
```json
{
  "success": true,
  "response": "Le budget minimum pour étudier en France est d'environ 8000€ par an..."
}
```

### 2. Analyse de profil (`POST /api/eadvisor/analyze`)
Génère des recommandations personnalisées selon l'étape.

**Request:**
```json
{
  "answers": {
    "currentLevel": "bac",
    "studyDestination": ["FR"],
    "france_budget": 10000
  },
  "stage": "step3_france"
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "recommendations": "Avec un budget de 10000€/an, tu peux candidater...",
    "stage": "step3_france"
  }
}
```

### 3. Suggestions (`POST /api/eadvisor/suggest`)
Génère des encouragements/suggestions après chaque étape.

**Request:**
```json
{
  "answers": {...},
  "currentStep": "step1",
  "nextQuestionId": "email"
}
```

**Response:**
```json
{
  "success": true,
  "suggestion": "Excellent ! Continuons avec ton adresse email..."
}
```

## 🎨 Intégration Frontend

### Étape 1 : Créer le service API

```javascript
// src/services/eadvisorService.js
const API_BASE = '/api/eadvisor';

export const eadvisorService = {
  async chat(question, conversationHistory = [], userProfile = {}) {
    const response = await fetch(`${API_BASE}/chat`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ question, conversationHistory, userProfile }),
    });
    return response.json();
  },

  async analyze(answers, stage) {
    const response = await fetch(`${API_BASE}/analyze`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ answers, stage }),
    });
    return response.json();
  },

  async suggest(answers, currentStep, nextQuestionId) {
    const response = await fetch(`${API_BASE}/suggest`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ answers, currentStep, nextQuestionId }),
    });
    return response.json();
  },
};
```

### Étape 2 : Utiliser dans EAdvisorWidget.jsx

**Dans `handleAnswer` :**
```javascript
const handleAnswer = async (value) => {
  // ... existing code ...
  
  // Optionnel : Analyse après réponse
  if (currentQuestion.id === 'studyDestination') {
    const analysis = await eadvisorService.analyze(answers, 'step2');
    if (analysis.success) {
      pushAssistant(analysis.data.recommendations);
    }
  }
  
  proceedNext();
};
```

**Dans la barre de chat (input) :**
```javascript
const handleChatInput = async (e) => {
  if (e.key === 'Enter' && e.target.value.trim()) {
    const question = e.target.value.trim();
    pushUser(question);
    e.target.value = '';
    
    setTyping(true);
    const response = await eadvisorService.chat(
      question,
      messages.map(m => ({ role: m.role, content: m.content })),
      answers
    );
    setTyping(false);
    
    if (response.success) {
      pushAssistant(response.response);
    }
  }
};
```

**Suggestions après chaque étape :**
```javascript
const proceedNext = async () => {
  // ... existing logic ...
  
  // Ajouter suggestion Grok (optionnel)
  const suggestion = await eadvisorService.suggest(
    answers,
    `step${stepNumber}`,
    nextQuestion?.id
  );
  
  if (suggestion.success && suggestion.suggestion) {
    setTimeout(() => pushAssistant(suggestion.suggestion), 500);
  }
};
```

## 📍 Étapes recommandées pour utiliser Grok

### ✅ **Étape 1 : Questions ouvertes (Input bar)**
- **Quand** : L'utilisateur tape une question libre
- **Endpoint** : `/api/eadvisor/chat`
- **Utilité** : Répondre aux questions hors flux structuré

### ✅ **Étape 2 : Après sélection de destination**
- **Quand** : Après réponse `studyDestination`
- **Endpoint** : `/api/eadvisor/analyze` avec `stage: 'step2'`
- **Utilité** : Recommander le meilleur choix pays selon profil

### ✅ **Étape 3A/B/C : Conseils contextuels**
- **Quand** : Après chaque sous-étape (France/Chine/Maroc)
- **Endpoint** : `/api/eadvisor/analyze` avec `stage: 'step3_france'|'step3_china'|'step3_morocco'`
- **Utilité** : Conseils spécifiques au pays (budget, procédures)

### ✅ **Étape 4 : Recommandations finales**
- **Quand** : Après `motivation` ou avant `userType`
- **Endpoint** : `/api/eadvisor/analyze` avec `stage: 'step4'`
- **Utilité** : Roadmap personnalisée et prochaines actions

### ✅ **Optionnel : Suggestions entre questions**
- **Quand** : Après chaque réponse importante
- **Endpoint** : `/api/eadvisor/suggest`
- **Utilité** : Encouragements et transitions fluides

## 🔒 Sécurité

- ✅ Tous les endpoints nécessitent `ROLE_USER`
- ✅ La clé API reste côté backend (jamais exposée)
- ✅ Rate limiting recommandé (à ajouter)
- ✅ Validation des entrées utilisateur

## 🚀 Déploiement

1. Ajouter `GROK_API_KEY` dans les variables d'environnement du serveur
2. Vérifier que HttpClient est configuré
3. Tester les endpoints avec Postman/Insomnia
4. Monitorer les logs d'erreur API Grok

## 📊 Monitoring

Vérifier les logs Symfony :
```bash
tail -f var/log/dev.log | grep "Grok"
```

## ⚠️ Notes importantes

- **Coût** : Grok API est payant par token. Surveiller l'usage.
- **Latence** : Les appels peuvent prendre 1-3 secondes. Ajouter un indicateur de chargement.
- **Fallback** : Si Grok échoue, afficher un message générique plutôt que d'erreur.
- **Cache** : Optionnellement cacher les analyses similaires pour réduire les appels API.

