# Railway Deployment Setup Guide

Complete guide for deploying 4 Projects.ai to Railway with all services.

## Prerequisites

1. Railway CLI installed: `npm install -g @railway/cli` or `brew install railway`
2. Logged into Railway: `railway login`
3. GitHub repository connected (recommended)

## Step 1: Initialize Railway Project

Run this command and follow the prompts:

```bash
railway init
```

**Options:**
- Select "Create a new project"
- Project name: `4projects` (or your preferred name)
- Select your team/organization

This creates a `.railway` directory with project configuration.

## Step 2: Add PostgreSQL Database

```bash
railway add --database postgres
```

This will:
- Create a PostgreSQL service
- Auto-generate `DATABASE_URL` and `POSTGRES_URL` environment variables
- Configure connection automatically

## Step 3: Add Redis Service

```bash
railway add --plugin redis
```

This will:
- Create a Redis service
- Auto-generate `REDIS_URL` environment variable
- Configure connection automatically

## Step 4: Set Environment Variables

### Core Application Variables

```bash
# Application
railway variables --set "APP_NAME=4 Projects.ai"
railway variables --set "APP_ENV=production"
railway variables --set "APP_DEBUG=false"
railway variables --set "LOG_LEVEL=info"

# Generate and set app key (run this locally first to get the key)
railway variables --set "APP_KEY=$(php artisan key:generate --show)"
```

**Or use the automated script:**
```bash
./railway-set-variables.sh
```

### Database (Auto-configured by Railway, but verify)

Railway automatically sets:
- `DATABASE_URL`
- `POSTGRES_URL`
- `POSTGRES_HOST`
- `POSTGRES_PORT`
- `POSTGRES_USER`
- `POSTGRES_PASSWORD`
- `POSTGRES_DATABASE`

Verify with:
```bash
railway variables | grep POSTGRES
```

### Redis (Auto-configured by Railway)

Railway automatically sets:
- `REDIS_URL`

Verify with:
```bash
railway variables | grep REDIS
```

### Cache and Queue Configuration

```bash
railway variables --set "CACHE_DRIVER=redis"
railway variables --set "SESSION_DRIVER=redis"
railway variables --set "QUEUE_CONNECTION=redis"
```

### Broadcasting and WebSockets (Reverb)

```bash
railway variables --set "BROADCAST_DRIVER=reverb"
railway variables --set "REVERB_APP_ID=4projects"

# Generate Reverb keys (run locally):
# php artisan reverb:install
# Then set the generated keys:
railway variables --set "REVERB_APP_KEY=your-generated-key""
railway variables --set "REVERB_APP_SECRET=your-generated-secret""
railway variables --set "REVERB_SCHEME=https"
railway variables --set "REVERB_HOST=your-app.railway.app""
railway variables --set "REVERB_PORT=443"
```

### OpenRouter AI

```bash
railway variables --set "OPENROUTER_API_KEY=your-openrouter-api-key""
railway variables --set "OPENROUTER_BASE_URL=https://openrouter.ai/api/v1"
railway variables --set "OPENROUTER_DEFAULT_MODEL=anthropic/claude-3.5-sonnet"
```

### Twilio (Optional - for SMS/Voice)

```bash
railway variables --set "TWILIO_SID=your-twilio-sid""
railway variables --set "TWILIO_TOKEN=your-twilio-token""
railway variables --set "TWILIO_PHONE=+15551234567"
```

### Slack (Optional - for Slack integration)

```bash
railway variables --set "SLACK_BOT_TOKEN=xoxb-your-slack-bot-token"
railway variables --set "SLACK_SIGNING_SECRET=your-signing-secret"
```

### Mail Configuration
"
```bash"
railway variables --set "MAIL_MAILER=smtp"
railway variables --set "MAIL_HOST=smtp.example.com"
railway variables --set "MAIL_PORT=587"
railway variables --set "MAIL_USERNAME=your-email@example.com""
railway variables --set "MAIL_PASSWORD=your-password""
railway variables --set "MAIL_ENCRYPTION=tls"
railway variables --set "MAIL_FROM_ADDRESS="tasks@4projects.ai"
```

### Get Your App URL (set after first deploy)

After first deployment, Railway will provide your app URL. Set it:

```bash
railway variables --set "APP_URL=https://your-app.railway.app"
```

## Step 5: Run Migrations

Before deploying, run migrations to set up the database schema:

```bash
railway run php artisan migrate
```

Or migrations will run automatically on first deploy (configured in `railway.json`).

## Step 6: Deploy Application

```bash
railway up
```

This will:
- Build your application using Nixpacks
- Install dependencies
- Run build commands
- Deploy to Railway

## Step 7: Add Worker Service for Horizon

Create a new service for the Horizon queue worker:

1. In Railway dashboard, click "New" → "GitHub Repo" (or use existing)
2. Select your repository
3. Change the start command to: `php artisan horizon`
4. Attach it to the same Redis service

Or use Railway CLI:

```bash
# Create a new service for Horizon
railway service create horizon-worker

# Set start command
railway variables --set "--service horizon-worker START_COMMAND="php artisan horizon"

# Link to same Redis and Database
railway link --service horizon-worker
```

## Step 8: Add Reverb Service (Optional)

For WebSocket support, add a separate Reverb service:

1. In Railway dashboard, create a new service
2. Set start command: `php artisan reverb:start`
3. Use environment variables from main service

## Service Architecture

Your Railway project should have:

1. **Web Service** (Main Laravel app)
   - Start: `php artisan serve --host=0.0.0.0 --port=$PORT`
   - Port: Auto-assigned by Railway

2. **PostgreSQL Service**
   - Auto-configured connection

3. **Redis Service**
   - Auto-configured connection

4. **Horizon Worker Service** (Recommended)
   - Start: `php artisan horizon`
   - Processes queues

5. **Reverb Service** (Optional)
   - Start: `php artisan reverb:start`
   - WebSocket server

## Verify Deployment

1. Check health endpoint: `https://your-app.railway.app/api/health`
2. View logs: `railway logs`
3. Connect to database: `railway connect postgres`
4. Check Redis: `railway run redis-cli`

## Useful Railway CLI Commands

```bash
# View all variables
railway variables

# Set a variable
railway variables --set "KEY=value

# View logs
railway logs

# Connect to database
railway connect postgres

# Run a command
railway run php artisan tinker

# Open dashboard
railway open

# View service status
railway status
```

## Troubleshooting

### Database Connection Issues

```bash
# Verify database variables are set
railway variables | grep POSTGRES

# Test connection
railway run php artisan db:show
```

### Redis Connection Issues

```bash
# Verify Redis variables
railway variables | grep REDIS

# Test Redis connection
railway run redis-cli ping
```

### Build Failures

```bash
# View build logs
railway logs --deployment

# Check nixpacks.toml configuration
cat nixpacks.toml
```

### Migration Issues

```bash
# Run migrations manually
railway run php artisan migrate --force

# Check migration status
railway run php artisan migrate:status
```

## Production Checklist

- [ ] All environment variables set
- [ ] APP_KEY generated and set
- [ ] Database migrations run successfully
- [ ] Redis service connected
- [ ] Horizon worker service running (if using queues)
- [ ] Reverb service running (if using WebSockets)
- [ ] APP_URL set correctly
- [ ] Health check endpoint responding
- [ ] SSL certificate active (automatic on Railway)
- [ ] OpenRouter API key configured (for AI features)
- [ ] Optional services configured (Twilio, Slack, Mail)

## Continuous Deployment

Railway automatically deploys when you push to your connected GitHub branch. Configure in Railway dashboard:
- Settings → Source → Connect GitHub
- Select repository and branch
- Railway will auto-deploy on push

## Scaling

- **Web Service**: Scale horizontally in Railway dashboard
- **Horizon Workers**: Scale based on queue load
- **Database**: Upgrade PostgreSQL plan in Railway dashboard
- **Redis**: Upgrade Redis plan if needed

## Monitoring

- View logs: `railway logs` or Railway dashboard
- Monitor Horizon: Access `/horizon` route (configured in HorizonServiceProvider)
- Health checks: Railway monitors `/api/health` endpoint
- Metrics: Available in Railway dashboard
"
"