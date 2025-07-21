# 🤖 AI Code Analyzer Toolkit

A modern, comprehensive AI-powered development toolkit that analyzes code quality across multiple languages and frameworks. Built with Laravel 12 and Vue 3, featuring both cloud and local AI providers for maximum flexibility, privacy, and cost control.

![Laravel](https://img.shields.io/badge/Laravel-12-red)
![Vue.js](https://img.shields.io/badge/Vue.js-3-green)
![Multi-AI](https://img.shields.io/badge/AI-Multi--Provider-blue)
![Local AI](https://img.shields.io/badge/Local-Ollama-purple)
![License](https://img.shields.io/badge/license-MIT-green)

## ✨ Features

### 🔍 **Smart Multi-Language Code Analysis**
- **Universal AI Analysis**: Supports PHP/Laravel, JavaScript, React, Vue.js, Node.js, React Native
- **Auto-Detection**: Automatically identifies language/framework and applies specific best practices
- **Quality Scoring**: Get instant quality scores (1-10) with framework-specific criteria
- **Actionable Suggestions**: Receive detailed, code-example-rich improvement recommendations
- **Security Analysis**: Identify vulnerabilities across all supported languages

### 🌐 **Multi-Provider AI Integration**
- **Cloud AI**: Google Gemini 2.0 Flash for high-quality, fast analysis
- **Local AI**: Ollama integration with Qwen2.5-Coder, DeepSeek-Coder, CodeLlama
- **Cost Control**: Choose between paid cloud AI or free local models
- **Privacy Options**: Keep sensitive code local with offline AI analysis
- **Provider Comparison**: Test same code with multiple AI providers

### 🎨 **Modern Interface**
- **Responsive Design**: Beautiful Vue.js interface that works on all devices
- **Real-time Analysis**: Live code analysis with progress indicators
- **Provider Selection**: Easy switching between AI providers and models
- **Syntax Highlighting**: Framework-specific code highlighting and formatting
- **Interactive Results**: Expandable suggestions with executable code examples

### 💾 **Advanced Analysis Management**
- **Save & Track**: Store analyses with custom names and provider information
- **History View**: Browse all past analyses with scores, timestamps, and costs
- **Detailed Modal**: View complete analysis details including original code
- **Provider Tracking**: See which AI provider and model generated each analysis
- **Cost Monitoring**: Track analysis costs across different providers

### 🏗️ **Technical Excellence**
- **Laravel 12**: Latest Laravel framework with Vue starter kit
- **Modern Stack**: Vue 3 + TypeScript + Tailwind CSS + Inertia.js
- **Multi-AI Architecture**: Extensible provider system with BaseProvider pattern
- **Local AI Ready**: Full Ollama integration for privacy-first development
- **Database Storage**: Enhanced schema tracking providers, models, and costs

## 🚀 Quick Start

### Prerequisites
- PHP 8.1+
- Composer
- Node.js 18+
- NPM/Yarn

### Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/rabibsust/ai-toolkit.git
   cd ai-toolkit
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node dependencies**
   ```bash
   npm install
   ```

4. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Configure Cloud AI (Optional)**
   ```bash
   # Get your API key from https://aistudio.google.com/
   # Add to .env file:
   GEMINI_API_KEY=your_gemini_api_key_here
   ```

6. **Setup Local AI (Recommended)**
   ```bash
   # Install Ollama (macOS/Linux)
   curl -fsSL https://ollama.com/install.sh | sh
   
   # Start Ollama service
   ollama serve
   
   # Download AI models for code analysis
   ollama pull qwen2.5-coder:7b          # Best overall performance
   ollama pull deepseek-coder:6.7b       # Efficient and fast
   ollama pull codellama:7b              # Security-focused
   ```

7. **Database setup**
   ```bash
   php artisan migrate
   ```

8. **Install Gemini Laravel package (if using cloud AI)**
   ```bash
   composer require google-gemini-php/laravel
   php artisan gemini:install
   ```

9. **Start development servers**
   ```bash
   composer run dev
   ```

Visit `http://127.0.0.1:8000` to start analyzing your code with AI!

## 📖 Usage

### Multi-Language Code Analysis
1. Navigate to the dashboard
2. Select your preferred AI provider (Gemini Cloud or Ollama Local)
3. Choose the AI model that best fits your needs
4. Paste your code (any supported language/framework)
5. The AI automatically detects the language and applies specific analysis
6. Review quality scores, suggestions, and security recommendations
7. Save analyses for future reference with cost tracking

### Supported Languages & Frameworks

| Language/Framework | Best AI Model | Specialization |
|-------------------|---------------|----------------|
| **PHP/Laravel** | Qwen2.5-Coder 7B | Framework patterns, security, Eloquent |
| **React** | Qwen2.5-Coder 7B | Hooks, performance, modern patterns |
| **Vue.js** | DeepSeek-Coder 6.7B | Composition API, reactivity, Vue 3 |
| **Node.js/Express** | CodeLlama 7B | Security, async patterns, APIs |
| **React Native** | Qwen2.5-Coder 7B | Mobile patterns, cross-platform |
| **JavaScript** | DeepSeek-Coder 6.7B | ES6+, browser compatibility, performance |

### Example Inputs

**Laravel Controller:**
```php
<?php

class UserController extends Controller 
{
    public function index()
    {
        $users = DB::table('users')->get();
        return view('users.index', compact('users'));
    }
    
    public function store(Request $request)
    {
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();
        return redirect()->back();
    }
}
```

**React Component:**
```jsx
import React, { useState, useEffect } from 'react';

function UserList() {
  const [users, setUsers] = useState([]);
  
  useEffect(() => {
    fetch('/api/users')
      .then(response => response.json())
      .then(data => setUsers(data));
  }, []);

  return (
    <div>
      {users.map(user => (
        <div key={user.id}>{user.name}</div>
      ))}
    </div>
  );
}
```

**Vue Component:**
```vue
<template>
  <div>
    <h1>{{ title }}</h1>
    <ul>
      <li v-for="user in users" :key="user.id">
        {{ user.name }}
      </li>
    </ul>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const title = ref('User List')
const users = ref([])

onMounted(async () => {
  const response = await fetch('/api/users')
  users.value = await response.json()
})
</script>
```

### Expected AI Analysis Output
- **Detected Language**: Auto-identified framework (e.g., "Laravel Controller", "React Component")
- **Quality Score**: 1-10 with framework-specific criteria
- **Suggestions**: Language-specific improvements with code examples
- **Security Analysis**: Framework-appropriate vulnerability detection
- **Best Practices**: Modern patterns and conventions
- **Performance Tips**: Optimization recommendations

## 🏗️ Architecture

### Backend (Laravel 12)
```
app/
├── Http/Controllers/
│   └── AiToolsController.php         # Main API controller
├── Services/
│   ├── LLMProviderFactory.php        # Multi-provider factory
│   └── Providers/
│       ├── BaseProvider.php          # Shared provider logic
│       ├── GeminiProvider.php        # Google Gemini integration
│       └── OllamaProvider.php        # Local Ollama integration
├── Contracts/
│   └── LLMProviderInterface.php      # Provider contract
└── Models/
    └── CodeAnalysis.php              # Enhanced analysis storage
```

### Frontend (Vue 3 + TypeScript)
```
resources/js/
├── pages/AiTools/
│   ├── Dashboard.vue                 # Multi-provider analysis interface
│   └── History.vue                   # Enhanced history with provider info
├── components/ui/                    # Reusable UI components
└── layouts/
    └── AppLayout.vue                 # Application layout
```

### Enhanced Database Schema
```sql
-- code_analyses table with provider tracking
CREATE TABLE code_analyses (
    id BIGINT PRIMARY KEY,
    code TEXT NOT NULL,
    analysis TEXT NOT NULL,
    suggestions JSON,
    score INTEGER,
    file_name VARCHAR(255),
    provider VARCHAR(50),              -- NEW: AI provider used
    model VARCHAR(100),                -- NEW: Specific model used
    cost DECIMAL(8,6),                 -- NEW: Analysis cost
    tokens_used INTEGER,               -- NEW: Token usage tracking
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

## 🛠️ Technology Stack

| Component | Technology | Purpose |
|-----------|------------|---------|
| **Backend** | Laravel 12 | API, routing, multi-provider logic |
| **Frontend** | Vue 3 + TypeScript | Reactive multi-language interface |
| **Styling** | Tailwind CSS | Responsive, modern design |
| **SPA** | Inertia.js | Seamless page transitions |
| **Cloud AI** | Google Gemini 2.0 | High-quality cloud analysis |
| **Local AI** | Ollama + Multiple Models | Privacy-first local analysis |
| **Database** | SQLite/PostgreSQL | Enhanced analysis storage |
| **Testing** | Pest | Modern PHP testing |

## 🤖 AI Provider Comparison

| Provider | Cost | Privacy | Speed | Quality | Best For |
|----------|------|---------|-------|---------|----------|
| **Gemini Cloud** | $0.001/request | ⭐⭐ | ⭐⭐⭐⭐⭐ | ⭐⭐⭐⭐⭐ | Production, complex analysis |
| **Ollama Local** | Free | ⭐⭐⭐⭐⭐ | ⭐⭐⭐ | ⭐⭐⭐⭐ | Privacy, development, learning |

### Local AI Models Included
- **Qwen2.5-Coder 7B**: Best overall performance (4.7GB)
- **DeepSeek-Coder 6.7B**: Most efficient (3.4GB)
- **CodeLlama 7B**: Security-focused (4.0GB)
- **CodeGemma 7B**: Google-optimized (4.2GB)

## 📊 Enhanced API Endpoints

### Analysis Endpoints
```http
POST /api/analyze-code
Content-Type: application/json

{
  "code": "<?php class UserController...",
  "provider": "ollama",
  "model": "qwen2.5-coder:7b",
  "options": {
    "focus": "security",
    "detail": "detailed"
  }
}
```

```http
POST /api/save-analysis
Content-Type: application/json

{
  "code": "...",
  "analysis": "...",
  "suggestions": [...],
  "score": 8,
  "file_name": "React Component Analysis",
  "provider": "ollama",
  "model": "qwen2.5-coder:7b"
}
```

```http
GET /api/analysis/{id}
GET /api/providers                    # Get available providers
```

## 🧪 Testing

Run the comprehensive test suite:
```bash
php artisan test
```

Test local AI integration:
```bash
# Test Ollama connection
ollama run qwen2.5-coder:7b "Analyze this PHP code: <?php echo 'hello'; ?>"
```

Run frontend tests:
```bash
npm run test
```

## 🚀 Deployment

### Production Setup
1. **Environment configuration**
   ```bash
   APP_ENV=production
   APP_DEBUG=false
   GEMINI_API_KEY=your_production_key    # Optional
   OLLAMA_URL=http://127.0.0.1:11434     # For local AI
   ```

2. **Database migration**
   ```bash
   php artisan migrate --force
   ```

3. **Asset optimization**
   ```bash
   npm run build
   php artisan config:cache
   php artisan route:cache
   ```

### Docker Deployment with AI
```dockerfile
# Multi-stage build with Ollama support
FROM php:8.2-fpm as app
# ... Laravel setup ...

FROM ollama/ollama as ai-models
RUN ollama pull qwen2.5-coder:7b
RUN ollama pull deepseek-coder:6.7b

# Production image combining both
FROM app
COPY --from=ai-models /root/.ollama /root/.ollama
```

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

### Development Guidelines
- Follow PSR-12 coding standards
- Write tests for new AI providers
- Update documentation for API changes
- Test with multiple AI models
- Use conventional commit messages

## 📝 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🎯 Updated Roadmap

### Phase 1: Multi-Provider Foundation ✅
- [x] Multi-LLM provider architecture
- [x] Cloud AI integration (Gemini)
- [x] Local AI integration (Ollama)
- [x] Multi-language support (PHP, JS, React, Vue, Node)
- [x] Auto-detection and smart analysis
- [x] Enhanced analysis history with provider tracking

### Phase 2: Smart Router & Advanced Features 🚧
- [ ] Intelligent AI router (auto-select best provider/model)
- [ ] Cost optimization algorithms
- [ ] Real-time provider performance monitoring
- [ ] Advanced security vulnerability scanning
- [ ] Code comparison across providers

### Phase 3: Team & Enterprise Features 🎯
- [ ] User authentication and multi-tenancy
- [ ] Team collaboration and shared analyses
- [ ] API rate limiting and quotas
- [ ] Advanced analytics dashboard
- [ ] Enterprise SSO integration

### Phase 4: Integration & Automation 🚀
- [ ] GitHub integration for PR analysis
- [ ] VS Code extension
- [ ] CI/CD pipeline integration
- [ ] Automated test generation
- [ ] Performance monitoring integration

## 📞 Support

- **Documentation**: [Wiki](https://github.com/rabibsust/ai-toolkit/wiki)
- **Issues**: [GitHub Issues](https://github.com/rabibsust/ai-toolkit/issues)
- **Discussions**: [GitHub Discussions](https://github.com/rabibsust/ai-toolkit/discussions)
- **Email**: rabib.sust@gmail.com

## 🌟 Acknowledgments

- **Google Gemini AI** for powerful cloud-based analysis
- **Ollama Team** for excellent local AI infrastructure
- **Alibaba Qwen Team** for Qwen2.5-Coder model
- **DeepSeek AI** for efficient code analysis models
- **Meta** for CodeLlama and React ecosystem
- **Laravel Team** for the excellent framework
- **Vue.js Team** for the reactive frontend framework
- **Tailwind CSS** for utility-first styling
- **Inertia.js** for seamless SPA functionality

---

**Built with ❤️ by Ahmad Jamaly Rabib**

*Transform your development workflow with AI-powered insights across all major languages and frameworks*

## 🔥 Key Differentiators

- **🌍 Universal Language Support**: One tool for PHP, JavaScript, React, Vue, Node.js, React Native
- **🏠 Privacy-First**: Run powerful AI models locally with zero cloud dependency
- **💰 Cost Flexible**: Choose between free local AI or premium cloud AI based on your needs
- **🧠 Smart Detection**: Automatically identifies languages and applies framework-specific analysis
- **📊 Provider Comparison**: Test the same code with multiple AI providers to get the best insights
- **🔒 Security Focused**: Framework-specific vulnerability detection across all supported languages
