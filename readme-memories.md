# Project Discussion and Planning

## Initial Discussion (March 24, 2025)

### Project Requirements
- Personal assistant and coach application
- Integration with local LLM (http://192.168.5.119:1234)
- Database for projects, todos, and personal diary
- Potential vector DB for RAG (Retrieval Augmented Generation)
- Model comparison feature for testing different LLMs

### Tech Stack Preferences
- Laravel as the backend framework
- Livewire for dynamic pages (limited use)
- Alpine.js for frontend interactivity
- Tailwind CSS for styling
- Blade templates for most pages
- Laravel Herd with PostgreSQL

### Questions to Address
- Database architecture (PostgreSQL + vector DB)
- API integration with local LLM
- UI/UX design for different application sections
- Authentication and data privacy

## Tech Stack Decision (March 24, 2025)

### Backend
- **Framework**: Laravel 10+ (latest stable version)
- **Database**: 
  - PostgreSQL for relational data (projects, todos, diary entries)
  - pgvector extension for vector storage (for RAG functionality)
- **LLM Integration**: 
  - HTTP client to communicate with local LLM API
  - Queue system for handling longer processing tasks

### Frontend
- **Primary**: Blade templates with minimal JavaScript
- **Interactive Components**: 
  - Alpine.js for simple interactivity
  - Livewire only for complex dynamic interfaces (chat interface, model comparison)
- **Styling**: Tailwind CSS with a simple, clean design

### Development Environment
- Laravel Herd for local development
- PostgreSQL with pgvector extension
- Version control with Git

## Database Architecture (ERD Planning)

### Core Entities

1. **Users**
   - id (PK)
   - name
   - email
   - password
   - settings (JSON)
   - created_at
   - updated_at

2. **Projects**
   - id (PK)
   - user_id (FK)
   - name
   - description
   - status (enum: active, paused, completed, abandoned)
   - priority (enum: low, medium, high)
   - start_date
   - target_completion_date
   - actual_completion_date
   - created_at
   - updated_at

3. **Tasks**
   - id (PK)
   - project_id (FK, nullable)
   - user_id (FK)
   - title
   - description
   - status (enum: todo, in_progress, completed)
   - priority (enum: low, medium, high)
   - due_date
   - completed_at
   - created_at
   - updated_at

4. **Diary_Entries**
   - id (PK)
   - user_id (FK)
   - title
   - content
   - mood (enum: happy, neutral, sad, anxious, productive, etc.)
   - created_at
   - updated_at

5. **Chat_Sessions**
   - id (PK)
   - user_id (FK)
   - title
   - created_at
   - updated_at

6. **Chat_Messages**
   - id (PK)
   - chat_session_id (FK)
   - role (enum: user, assistant)
   - content
   - created_at

7. **Chat_Embeddings**
   - id (PK)
   - chat_message_id (FK)
   - embedding (vector)
   - created_at
   - updated_at

8. **Model_Comparisons**
   - id (PK)
   - user_id (FK)
   - prompt
   - models_compared (JSON)
   - created_at

9. **Model_Responses**
   - id (PK)
   - comparison_id (FK)
   - model_name
   - response_content
   - tokens_used
   - response_time
   - created_at

## Application Features

### Personal Assistant & Coach
- Chat interface to communicate with LLM
- Context-aware responses using RAG from user's projects, tasks, and diary
- Accountability features (reminders, progress tracking)
- Goal setting and monitoring

### Project & Task Management
- CRUD operations for projects and tasks
- Status tracking and priority management
- Deadline monitoring and notifications
- Progress visualization

### Personal Diary
- Daily/periodic entries
- Mood tracking
- Searchable content
- Integration with assistant for insights

### Model Comparison Tool
- Interface to test different LLM models
- Side-by-side comparison of outputs
- Performance metrics (response time, token usage)
- Saving favorite prompts and responses

## Next Steps
1. Set up Laravel project with authentication
2. Configure PostgreSQL with pgvector
3. Create database migrations for core entities
4. Implement basic CRUD operations for projects and tasks
5. Develop chat interface for LLM integration
6. Implement vector storage for RAG functionality
7. Create model comparison feature
8. Refine UI/UX with Tailwind CSS
