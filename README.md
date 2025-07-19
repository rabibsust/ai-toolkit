# 🤖 AI Code Analyzer Toolkit

A modern, AI-powered Laravel development toolkit that analyzes code quality, provides intelligent suggestions, and helps developers improve their Laravel applications using cutting-edge AI technology.

![AI Code Analyzer](https://img.shields.io/badge/Laravel-12-red)
![Vue.js](https://img.shields.io/badge/Vue.js-3-green)
![AI Powered](https://img.shields.io/badge/AI-Gemini%202.0-blue)
![License](https://img.shields.io/badge/license-MIT-green)

## ✨ Features

### 🔍 **Smart Code Analysis**
- **AI-Powered Reviews**: Advanced code analysis using Google's Gemini 2.0 Flash
- **Quality Scoring**: Get instant quality scores (1-10) based on Laravel best practices
- **Actionable Suggestions**: Receive specific, code-example-rich improvement recommendations
- **Security Analysis**: Identify potential security vulnerabilities and mass assignment issues

### 🎨 **Modern Interface**
- **Responsive Design**: Beautiful Vue.js interface that works on all devices
- **Real-time Analysis**: Live code analysis with progress indicators
- **Syntax Highlighting**: Code blocks with proper PHP syntax highlighting
- **Interactive Results**: Expandable suggestions with code examples

### 💾 **Analysis Management**
- **Save & Track**: Store analyses with custom names for future reference
- **History View**: Browse all past analyses with scores and timestamps
- **Detailed Modal**: View complete analysis details including original code
- **Export Ready**: Foundation for PDF exports and reporting

### 🏗️ **Technical Excellence**
- **Laravel 12**: Latest Laravel framework with Vue starter kit
- **Modern Stack**: Vue 3 + TypeScript + Tailwind CSS + Inertia.js
- **AI Integration**: Google Gemini API with robust error handling
- **Database Storage**: SQLite for development, easily configurable for production

## 🚀 Quick Start

### Prerequisites
- PHP 8.1+
- Composer
- Node.js 18+
- NPM/Yarn

### Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/yourusername/ai-toolkit.git
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

5. **Configure Gemini AI**
   ```bash
   # Get your API key from https://aistudio.google.com/
   # Add to .env file:
   GEMINI_API_KEY=your_gemini_api_key_here
   ```

6. **Database setup**
   ```bash
   php artisan migrate
   ```

7. **Install Gemini Laravel package**
   ```bash
   composer require google-gemini-php/laravel
   php artisan gemini:install
   ```

8. **Start development servers**
   ```bash
   composer run dev
   ```

Visit `http://localhost:8000` to start analyzing your Laravel code!

## 📖 Usage

### Basic Code Analysis
1. Navigate to the dashboard
2. Paste your Laravel controller code in the input area
3. Click "Analyze Code" 
4. Review the quality score and suggestions
5. Save the analysis for future reference

### Example Input
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

### Example Output
- **Quality Score**: 6/10
- **Suggestions**: 
  - Use Eloquent models instead of DB facade
  - Add request validation in store method
  - Implement proper error handling
  - Add authorization checks

## 🏗️ Architecture

### Backend (Laravel 12)
```
app/
├── Http/Controllers/
│   └── AiToolsController.php     # Main controller
├── Services/
│   └── AiAnalyzerService.php     # AI analysis logic
└── Models/
    └── CodeAnalysis.php          # Analysis storage model
```

### Frontend (Vue 3 + TypeScript)
```
resources/js/
├── pages/AiTools/
│   ├── Dashboard.vue             # Main analysis interface
│   └── History.vue               # Analysis history
├── components/ui/                # Reusable UI components
└── layouts/
    └── AppLayout.vue             # Application layout
```

### Database Schema
```sql
-- code_analyses table
id, code, analysis, suggestions, score, file_name, created_at, updated_at
```

## 🛠️ Technology Stack

| Component | Technology | Purpose |
|-----------|------------|---------|
| **Backend** | Laravel 12 | API, routing, business logic |
| **Frontend** | Vue 3 + TypeScript | Reactive user interface |
| **Styling** | Tailwind CSS | Utility-first styling |
| **SPA** | Inertia.js | Seamless page transitions |
| **AI Engine** | Google Gemini 2.0 | Code analysis and suggestions |
| **Database** | SQLite/PostgreSQL | Analysis storage |
| **Testing** | Pest | Modern PHP testing |

## 📊 API Endpoints

### Analysis Endpoints
```http
POST /api/analyze-code
Content-Type: application/json

{
  "code": "<?php class UserController..."
}
```

```http
POST /api/save-analysis
Content-Type: application/json

{
  "code": "...",
  "analysis": "...",
  "suggestions": [...],
  "score": 6,
  "file_name": "UserController Analysis"
}
```

```http
GET /api/analysis/{id}
```

## 🧪 Testing

Run the test suite:
```bash
php artisan test
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
   GEMINI_API_KEY=your_production_key
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

### Docker Deployment
```dockerfile
# Dockerfile included for containerized deployment
docker build -t ai-toolkit .
docker run -p 8000:8000 ai-toolkit
```

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

### Development Guidelines
- Follow PSR-12 coding standards
- Write tests for new features
- Update documentation for API changes
- Use conventional commit messages

## 📝 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🎯 Roadmap

### Phase 1: Core Enhancement ✅
- [x] AI-powered code analysis
- [x] Quality scoring system
- [x] Analysis history and storage
- [x] Responsive Vue.js interface

### Phase 2: Advanced Features 🚧
- [ ] Multiple LLM provider support (OpenAI, Claude, Local LLMs)
- [ ] User authentication and multi-tenancy
- [ ] PDF report generation
- [ ] Code comparison features
- [ ] GitHub integration

### Phase 3: SaaS Platform 🎯
- [ ] Subscription management
- [ ] Team collaboration features
- [ ] API rate limiting and quotas
- [ ] Advanced analytics dashboard
- [ ] Enterprise SSO integration

### Phase 4: AI Expansion 🚀
- [ ] Test generation automation
- [ ] Deployment optimization suggestions
- [ ] Performance analysis
- [ ] Security vulnerability detection
- [ ] Code refactoring recommendations

## 📞 Support

- **Documentation**: [Wiki](https://github.com/rabibsust/ai-toolkit/wiki)
- **Issues**: [GitHub Issues](https://github.com/rabibsust/ai-toolkit/issues)
- **Discussions**: [GitHub Discussions](https://github.com/rabibsust/ai-toolkit/discussions)
- **Email**: support@youraitooltkit.com

## 🌟 Acknowledgments

- **Google Gemini AI** for powerful code analysis capabilities
- **Laravel Team** for the excellent framework
- **Vue.js Team** for the reactive frontend framework
- **Tailwind CSS** for the utility-first styling approach
- **Inertia.js** for seamless SPA functionality

---

**Built with ❤️ by Ahmad Jamaly Rabib**

*Transform your Laravel development workflow with AI-powered insights*
