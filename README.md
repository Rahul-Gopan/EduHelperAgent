# EduHelperAgent — AI Educational Chatbot

A Laravel-based AI chatbot built with [LarAgent](https://github.com/MaestroError/LarAgent) that helps school students learn three topics: **Solar System**, **Fractions**, and **Water Cycle**.

---

## Tech Stack

| Layer | Technology |
|---|---|
| Framework | Laravel 12 |
| AI Agent | LarAgent `^1.3` |
| LLM Provider | Groq (free) — `llama-3.3-70b-versatile` |
| Session Memory | Laravel Session driver |
| Frontend | Blade + Vanilla JS |

---

## Project Structure

```
app/
├── Agents/
│   └── EduHelperAgent.php      ← AI agent (instructions, model, memory)
├── Http/Controllers/
│   └── ChatController.php      ← Handles POST /chat, ties session to agent
resources/views/
│   └── chat.blade.php          ← Full chat UI (bubbles, typing indicator)
routes/
│   └── web.php                 ← GET /chat (view) + POST /chat (API)
```

---

## Requirements

- PHP >= 8.2
- Composer
- A free [Groq API key](https://console.groq.com/keys)

---

## Installation & Setup

### 1. Clone the repository

```bash
git clone <your-repo-url>
cd edu-helper-agent
```

### 2. Install dependencies

```bash
composer install
```

### 3. Configure environment

```bash
cp .env.example .env
php artisan key:generate
```

Open `.env` and set your Groq API key:

```env
GROQ_API_KEY=gsk_your_key_here
```

### 4. Run migrations

```bash
php artisan migrate
```

> The project uses SQLite by default — no database setup needed.

### 5. Start the server

```bash
php artisan serve
```

Visit **http://localhost:8000/chat**

---

## How the Agent Works

### `EduHelperAgent.php`

```php
protected $provider = 'groq';               // Uses Groq (free LLM)
protected $model    = 'llama-3.3-70b-versatile';
protected $history  = 'session';            // Conversation memory per user
```

- **Provider** — Groq is configured in `config/laragent.php` and reads `GROQ_API_KEY` from `.env`
- **Memory** — `$history = 'session'` stores the full conversation in Laravel's session, so the bot remembers previous messages within the same browser session
- **Instructions** — The `instructions()` method defines the agent's behaviour: greet politely, answer only 3 topics, stay under 60 words, and reject off-topic questions with a fixed message

### `ChatController.php`

```php
$sessionKey = $request->session()->getId();   // Unique key per user
$agent      = new EduHelperAgent($sessionKey); // Agent loads that session's history
$response   = $agent->respond($request->message);
```

Each user gets their own isolated conversation history via their session ID.

### Chat UI (`chat.blade.php`)

- Topic quick-start chips (Solar System / Fractions / Water Cycle)
- Animated typing indicator while waiting for the AI response
- User messages on the right (purple), bot messages on the left (grey)
- Error state shown inline if the API call fails

---

## Agent Behaviour

| Scenario | Response |
|---|---|
| First message | Warm greeting + topic list |
| Question about Solar System | Answer ≤ 60 words |
| Question about Fractions | Answer ≤ 60 words |
| Question about Water Cycle | Answer ≤ 60 words |
| Off-topic question | `"I can only help with Solar System, Fractions, or Water Cycle for now."` |
| Follow-up question | Remembers previous messages in the session |

---

## Environment Variables

| Variable | Description |
|---|---|
| `GROQ_API_KEY` | Your free Groq API key |
| `SESSION_DRIVER` | Set to `database` (default) |
| `DB_CONNECTION` | Set to `sqlite` (default) |

---

## Getting a Free Groq API Key

1. Go to [console.groq.com/keys](https://console.groq.com/keys)
2. Sign up for a free account
3. Click **Create API Key**
4. Copy the key into your `.env` file as `GROQ_API_KEY`

Groq's free tier supports **14,400 requests/day** — more than enough for development and demos.
