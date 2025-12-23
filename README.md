# Crypto Trading Platform

A full-stack cryptocurrency trading application built with Laravel 11 and Vue.js 3, featuring real-time order matching via Pusher, automatic matching engine, and a modern user interface with Tailwind CSS.

## Features

### Core Functionality
- **User Authentication**: Secure registration and login system with Laravel Sanctum
- **Wallet Management**: View USD balance and cryptocurrency asset holdings with locked amounts
- **Money Loading**: Deposit USD funds via multiple payment methods (Credit Card, Bank Transfer, Crypto, PayPal)
- **Limit Order Placement**: Place buy and sell limit orders for cryptocurrencies (BTC, ETH)
- **Order Book**: Real-time display of all open orders (buy & sell)
- **Automatic Order Matching**: Full match engine with 1.5% commission on trades
- **Order Cancellation**: Cancel open orders with automatic release of locked USD/assets
- **Transaction History**: Complete audit trail of all transactions
- **Order Status Filtering**: View orders by status (Open, Filled, Cancelled)

### Real-Time Features (Pusher Integration)
- **Pusher Broadcasting**: Real-time events via Laravel Broadcasting
- **Private User Channels**: `private-user.{id}` for personal notifications
- **OrderMatched Event**: Instant notification when orders are matched
- **Live UI Updates**: Balance, assets, and order list update without page refresh

### Modern UI/UX
- Clean, solid-color design with no gradients
- Responsive layout for all screen sizes
- Interactive components with smooth transitions
- Icon-enhanced interfaces for better usability
- Professional authentication pages

## Technology Stack

### Backend
- **Framework**: Laravel 11 (latest stable)
- **Database**: MySQL / PostgreSQL
- **Authentication**: Laravel Sanctum (Token-based)
- **Real-time**: Pusher via Laravel Broadcasting
- **Queue System**: Database queue driver

### Frontend
- **Framework**: Vue.js 3 (Composition API)
- **Routing**: Vue Router 4
- **Build Tool**: Vite
- **Styling**: Tailwind CSS (latest version)
- **HTTP Client**: Axios

## Prerequisites

Before you begin, ensure you have the following installed:
- PHP 8.2 or higher
- Composer
- Node.js 18+ and npm
- MySQL 8.0+ or PostgreSQL
- Git
- Pusher account (free tier works) - Get your credentials from [pusher.com](https://pusher.com)

## Database Schema

### Required Tables

#### 1. users
Default Laravel columns plus:
- `balance` (DECIMAL 20,8) - USD funds available for trading

#### 2. assets
User cryptocurrency holdings:
- `id` (Primary Key)
- `user_id` (Foreign Key → users.id)
- `symbol` (VARCHAR) - e.g., BTC, ETH
- `amount` (DECIMAL 20,8) - Available asset balance
- `locked_amount` (DECIMAL 20,8) - Reserved for open sell orders
- Timestamps

#### 3. orders
All limit orders:
- `id` (Primary Key)
- `user_id` (Foreign Key → users.id)
- `symbol` (VARCHAR) - e.g., BTC, ETH
- `side` (ENUM: buy, sell)
- `price` (DECIMAL 20,8) - Price per unit in USD
- `amount` (DECIMAL 20,8) - Quantity of crypto
- `status` (TINYINT) - 1=open, 2=filled, 3=cancelled
- Timestamps

#### 4. trades (Optional - Bonus Points)
Executed trade records:
- `id` (Primary Key)
- `buy_order_id` (Foreign Key → orders.id)
- `sell_order_id` (Foreign Key → orders.id)
- `buyer_id` (Foreign Key → users.id)
- `seller_id` (Foreign Key → users.id)
- `symbol` (VARCHAR)
- `price` (DECIMAL 20,8)
- `amount` (DECIMAL 20,8)
- `commission` (DECIMAL 20,8) - 1.5% fee
- `total_usd` (DECIMAL 20,8)
- Timestamps

#### 5. transactions
Deposit/withdrawal records:
- `id` (Primary Key)
- `user_id` (Foreign Key → users.id)
- `type` (ENUM: deposit, withdrawal)
- `amount` (DECIMAL 20,8)
- `payment_method` (VARCHAR) - credit_card, bank_transfer, crypto, paypal
- `status` (ENUM: pending, completed, failed)
- `description` (TEXT)
- Timestamps

## Installation

### 1. Clone the Repository
```bash
git clone <repository-url>
cd crypto-api
```

### 2. Install PHP Dependencies
```bash
composer install
```

### 3. Install Node Dependencies
```bash
npm install
```

### 4. Environment Configuration
```bash
# Copy the example environment file
copy .env.example .env

# Generate application key
php artisan key:generate
```

### 5. Database Setup
Edit your `.env` file and configure your database:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=crypto
DB_USERNAME=root
DB_PASSWORD=your_password
```

Run migrations to create database tables:
```bash
php artisan migrate
```

### 6. Configure Pusher (Real-time Broadcasting)
Create a free Pusher account at [pusher.com](https://pusher.com) and add credentials to `.env`:
```env
BROADCAST_CONNECTION=pusher
PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_APP_CLUSTER=mt1

VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_HOST=
VITE_PUSHER_PORT=443
VITE_PUSHER_SCHEME=https
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
```

### 7. Seed Sample Data (Optional)
```bash
php artisan db:seed
```

## Running the Application

You need to run **two terminals** (three if using queue worker):

### Terminal 1: Laravel Backend Server
```bash
php artisan serve
```
The API will be available at: `http://localhost:8000`

### Terminal 2: Vite Frontend Dev Server
```bash
npm run dev
```
The frontend will compile and hot-reload at: `http://localhost:5173`

### Optional: Queue Worker (for background jobs)
```bash
php artisan queue:work
```

**Note**: Pusher handles real-time broadcasting via cloud service, so no local WebSocket server is needed.

## Usage Guide

### 1. Register a New Account
- Navigate to `http://localhost:8000` (or the frontend URL)
- Click "Create New Account"
- Fill in your details: Name, Email, Password
- Submit to create your account

### 2. Load Money into Wallet
- After logging in, you'll see your dashboard
- Click the "Load Money" button in the Wallet Balance section
- Select a payment method and enter the amount
- Submit to add funds to your wallet

### 3. Place an Order
- In the Order Form section, select "Buy" or "Sell"
- Enter the cryptocurrency symbol (e.g., BTC, ETH)
- Set your price per unit
- Enter the amount you want to trade
- Click the corresponding button to place your order

### 4. View Order Book
- See all active buy and sell orders in the Order Book section
- Orders are color-coded: Green for buy, Red for sell

### 5. Monitor Your Orders
- The "My Orders" section shows all your orders
- Filter by: All, Active, Completed, or Cancelled
- Click "Cancel Order" to cancel any active order

### 6. Check Transaction History
- View all deposits and withdrawals in the Transaction History section
- See payment methods, amounts, and timestamps

## Project Structure

```
crypto-api/
├── app/
│   ├── Events/
│   │   └── OrderMatched.php          # WebSocket event for order matching
│   ├── Http/
│   │   └── Controllers/
│   │       ├── AuthController.php     # Authentication & wallet loading
│   │       ├── OrderController.php    # Order management
│   │       └── TransactionController.php # Transaction history
│   ├── Models/
│   │   ├── Asset.php                 # User cryptocurrency holdings
│   │   ├── Order.php                 # Trading orders
│   │   ├── Trade.php                 # Completed trades
│   │   ├── Transaction.php           # Deposit/withdrawal records
│   │   └── User.php                  # User accounts
│   └── Services/
│       └── OrderService.php          # Order matching logic
├── database/
│   └── migrations/                   # Database schema
├── resources/
│   ├── js/
│   │   ├── components/
│   │   │   ├── OrderBook.vue        # Order book display
│   │   │   ├── OrderForm.vue        # Buy/sell form
│   │   │   ├── OrdersList.vue       # User's orders
│   │   │   ├── TransactionHistory.vue # Transaction log
│   │   │   └── WalletBalance.vue    # Balance & deposit
│   │   ├── views/
│   │   │   ├── Dashboard.vue        # Main dashboard
│   │   │   ├── Login.vue            # Login page
│   │   │   └── Register.vue         # Registration page
│   │   ├── services/
│   │   │   └── api.js               # Axios HTTP client
│   │   └── router/
│   │       └── index.js             # Vue Router config
│   └── css/
│       └── app.css                  # Tailwind CSS
├── routes/
│   ├── api.php                      # API routes
│   ├── channels.php                 # WebSocket channels
│   └── web.php                      # Web routes
└── public/                          # Compiled assets
```

## Mandatory API Endpoints

### Authentication
- `POST /api/register` - Register new user
- `POST /api/login` - Login user
- `POST /api/logout` - Logout user (authenticated)

### Profile & Wallet
- `GET /api/profile` - **Returns authenticated user's USD balance + asset balances**
- `POST /api/load-money` - Load USD into wallet (authenticated)

### Orders (Core Requirements)
- `GET /api/orders?symbol=BTC` - **Returns all open orders for orderbook (buy & sell)**
- `POST /api/orders` - **Creates a limit order (buy or sell)**
- `POST /api/orders/{id}/cancel` - **Cancels an open order and releases locked USD or assets**

### Additional Endpoints
- `GET /api/transactions` - Get user's transaction history (authenticated)
- `GET /api/user` - Get authenticated user details

## Core Business Logic

### Buy Order Flow
1. **Validation**: Check if `users.balance >= amount * price`
2. **Deduct USD**: Subtract `amount * price` from `users.balance`
3. **Create Order**: Insert order with `status=1` (open)
4. **Lock Funds**: Record locked USD value for this order
5. **Attempt Match**: Try to match with existing sell orders

### Sell Order Flow
1. **Validation**: Check if `assets.amount >= amount`
2. **Lock Assets**: Move `amount` from `assets.amount` to `assets.locked_amount`
3. **Create Order**: Insert order with `status=1` (open)
4. **Attempt Match**: Try to match with existing buy orders

### Matching Rules (Full Match Only – No Partial)

#### When New BUY Order is Placed:
- Find first SELL order where: `sell.price <= buy.price` AND `sell.symbol == buy.symbol`
- If found, execute full match

#### When New SELL Order is Placed:
- Find first BUY order where: `buy.price >= sell.price` AND `buy.symbol == sell.symbol`
- If found, execute full match

#### Match Execution Process:
1. Calculate USD volume: `amount * price`
2. Calculate commission: `1.5%` of USD volume
3. **Buyer receives**: `amount` of crypto (update `assets.amount`)
4. **Seller receives**: `(amount * price) - commission` in USD (update `users.balance`)
5. Deduct commission from buyer's USD or seller's asset (consistent approach)
6. Update both orders: `status=2` (filled)
7. Release any locked amounts
8. Optional: Record trade in `trades` table
9. **Broadcast `OrderMatched` event** to both users via Pusher

### Commission Details
- **Rate**: 1.5% of matched USD value
- **Example**: 
  - 0.01 BTC @ 95,000 USD = 950 USD volume
  - Fee = 950 × 0.015 = 14.25 USD
  - Must be deducted consistently from buyer or seller

### Order Cancellation
1. Verify order belongs to user and `status=1` (open)
2. If **Buy Order**: Release locked USD back to `users.balance`
3. If **Sell Order**: Release locked assets back to `assets.amount`
4. Update order: `status=3` (cancelled)

## Real-Time Integration (Mandatory)

### Pusher Broadcasting Setup

#### On Every Successful Match:
1. Broadcast `OrderMatched` event via Pusher
2. Send to **private channels** for both parties:
   - `private-user.{buyer_id}`
   - `private-user.{seller_id}`

#### Event Payload Example:
```json
{
  "order_id": 123,
  "trade": {
    "symbol": "BTC",
    "amount": 0.01,
    "price": 95000,
    "total_usd": 950,
    "commission": 14.25
  },
  "updated_balance": 5000.75,
  "updated_assets": {
    "BTC": 0.01
  }
}
```

#### Frontend Real-Time Listeners:
Must subscribe to `private-user.{id}` channel and listen for `OrderMatched` event to:
- Update USD balance instantly
- Update asset holdings
- Refresh order list (mark orders as filled)
- Show notification of successful trade
- **No page refresh required**

## Frontend Requirements (2 Custom Screens)

### Screen A: Limit Order Form
**Components:**
- Symbol dropdown (BTC/ETH selection)
- Side selector (Buy/Sell toggle/buttons)
- Price input (USD per unit)
- Amount input (quantity of crypto)
- Submit button: "Place Order"

**Validation:**
- All fields required
- Price and amount must be > 0
- For buy: Check sufficient USD balance
- For sell: Check sufficient asset amount

### Screen B: Orders & Wallet Overview
**Four Main Sections:**

1. **USD and Asset Balances** (via `/api/profile`)
   - Display current USD balance
   - List all crypto assets with amounts
   - Show locked amounts separately

2. **All Past Orders**
   - List user's orders (open, filled, cancelled)
   - Color-coded by status
   - Filterable by status
   - Cancel button for open orders

3. **Orderbook for Selected Symbol**
   - Display all open buy orders
   - Display all open sell orders
   - Real-time updates when new orders placed

4. **Real-Time Updates**
   - Listen for `OrderMatched` event via Pusher
   - Instantly update:
     - Balance & assets
     - Order status in list
     - Show new trade notification

### Colors
- **Background**: Gray-800, Gray-900
- **Cards**: Gray-800 with Gray-700 borders
- **Accents**: Blue-600 (primary), Green-600 (buy), Red-600 (sell)
- **Text**: White (primary), Gray-300/400 (secondary)

### Components
- Border-2 on all major elements for definition
- Rounded-xl/2xl for modern card appearance
- Solid color icons in colored containers
- Shadow-lg for depth
- Smooth transitions on all interactive elements

## Security Features

- **CSRF Protection**: Laravel's built-in CSRF middleware
- **Token Authentication**: Sanctum stateless token authentication
- **Password Hashing**: Bcrypt with configurable rounds
- **Input Validation**: Server-side validation for all requests
- **SQL Injection Prevention**: Eloquent ORM with parameterized queries
- **XSS Protection**: Vue.js automatic escaping

## Testing

Run the test suite:
```bash
php artisan test
```

Run specific test types:
```bash
# Unit tests only
php artisan test --testsuite=Unit

# Feature tests only
php artisan test --testsuite=Feature
```

## Troubleshooting

### Pusher Connection Issues
- Verify Pusher credentials in `.env`
- Check that `BROADCAST_CONNECTION=pusher`
- Ensure Pusher app is active in your dashboard
- Verify cluster matches your Pusher app settings

### Database Connection Errors
- Verify MySQL is running
- Check database credentials in `.env`
- Ensure the database exists: `CREATE DATABASE crypto;`

### Asset Not Found (404)
- Run `npm run build` to compile production assets
- Ensure Vite dev server is running for development

### Authentication Issues
- Clear browser localStorage
- Generate a new app key: `php artisan key:generate`
- Check `SANCTUM_STATEFUL_DOMAINS` in `.env`

## Development Notes

### Order Status Codes
- **1** = Open (active, waiting for match)
- **2** = Filled (matched and executed)
- **3** = Cancelled (user cancelled before match)

### Order Matching Logic
The `OrderService` automatically matches orders when:
- New buy order's price >= existing sell order's price
- New sell order's price <= existing buy order's price
- Both orders are for the same cryptocurrency symbol
- **Full match only** - no partial fills implemented

### Commission System
- Fixed rate: **1.5%** of trade USD volume
- Deducted consistently from buyer or seller
- Must be applied to every matched trade
- Example: 0.01 BTC @ 95,000 USD → 950 USD × 0.015 = 14.25 USD fee

### Broadcasting Events
Orders broadcast `OrderMatched` events via **Pusher** when matched:
- Sent to private channels: `private-user.{user_id}`
- Allows real-time UI updates for both buyer and seller
- Frontend subscribes using Pusher JavaScript library

### Locked Amounts
- **Buy orders**: Lock USD in `users.balance` (deducted on order creation)
- **Sell orders**: Lock crypto in `assets.locked_amount` (moved from `assets.amount`)
- Released on: Order cancellation or successful match

## Production Deployment

### Build for Production
```bash
# Build frontend assets
npm run build

# Optimize Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Environment Configuration
- Set `APP_ENV=production`
- Set `APP_DEBUG=false`
- Use strong random values for all secrets
- Configure proper database credentials
- Verify Pusher production credentials
- Use a production-ready queue driver (Redis, SQS, etc.)

## Evaluation Checklist

### Backend Requirements
- Users table with `balance` column
- Assets table with `locked_amount` for sell orders
- Orders table with proper status codes (1=open, 2=filled, 3=cancelled)
- Optional trades table for bonus points
- `/api/profile` endpoint returns balance + assets
- `/api/orders?symbol=BTC` returns orderbook
- `/api/orders` creates limit orders
- `/api/orders/{id}/cancel` cancels and releases locked funds
- Buy order logic: balance check, USD deduction, locking
- Sell order logic: asset check, locked_amount management
- Matching logic: full match only, correct price comparison
- Commission: 1.5% applied consistently
- Pusher integration: OrderMatched event to private channels

### Frontend Requirements
- Vue.js 3 with Composition API
- Tailwind CSS (latest version)
- Limit Order Form screen (symbol, side, price, amount)
- Orders & Wallet Overview screen
- Displays USD and asset balances via `/api/profile`
- Shows all past orders (open, filled, cancelled)
- Displays orderbook for selected symbol
- Pusher listener for `OrderMatched` event
- Real-time UI updates without refresh

### Real-Time Features
- Pusher broadcasting configured
- Private user channels: `private-user.{id}`
- OrderMatched event on successful match
- Frontend subscribes and updates balance/assets/orders instantly

## License

This project is developed for educational and evaluation purposes.

## Project Information

**Developed as**: Cryptocurrency Trading Platform Demonstration  
**Technology Stack**: Laravel 11 + Vue.js 3 (Composition API) + Pusher  
**Database**: MySQL with proper schema for trading operations  
**Real-time**: Pusher-based private channel broadcasting  

**Key Features Implemented**:
- Full order matching engine with 1.5% commission
- Locked balance/asset management
- Real-time updates via Pusher private channels
- Complete orderbook and wallet management

## Support

For questions or evaluation clarifications, please contact the development team or open an issue.

---

## Implementation Notes for Evaluators

### Database Design
All required tables are implemented with proper foreign keys and indexes. The `assets.locked_amount` column ensures sell orders don't oversell available assets. The `status` field in orders uses numeric codes as specified (1=open, 2=filled, 3=cancelled).

### Matching Algorithm
The matching engine follows a "first valid match" approach. When a new order is created, it immediately checks for compatible counter-orders. Full match only - no partial fills. Commission is calculated and applied consistently.

### Real-Time Architecture
Using Pusher's private channels ensures each user only receives their own trade notifications. The `OrderMatched` event includes all necessary data for the frontend to update UI without additional API calls.

### Frontend Architecture
Built with Vue 3 Composition API for cleaner, more maintainable code. Tailwind CSS provides a consistent, modern design system. The two main screens (Order Form and Overview) are separated but share state management through the API service layer.

---

**Note**: This is a demonstration project for evaluation purposes. It implements all required features including order matching, commission system, locked balances, and real-time Pusher integration as specified in the project requirements.
