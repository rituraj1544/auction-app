# Real-Time Online Auction Platform

A Laravel 12.53.0 full-stack auction system with separate user/admin authentication, a Tailwind + Alpine marketplace UI, a separate SaaS-style admin dashboard, real-time bidding, auction timers, notifications, watchlists, search/filtering, seed data, and analytics.

## Folder Structure

- `app/Models`: `User`, `Category`, `Auction`, `Bid` Eloquent models and relationships.
- `app/Http/Controllers`: public auction, bid, dashboard, profile, auth, and admin controllers.
- `app/Http/Requests`: form request validation for auctions and bids.
- `app/Policies`: auction edit/delete authorization rules.
- `app/Events`: `BidPlaced` broadcast event.
- `app/Notifications`: outbid, winner, and ending-soon notifications.
- `app/Console/Commands`: scheduled auction closing and ending-soon reminders.
- `database/migrations`: users, categories, auctions, bids, notifications, jobs, cache, sessions.
- `database/migrations/*auction_requests*`: user sell request moderation workflow.
- `database/factories` and `database/seeders`: demo users, categories, auctions, and bids.
- `resources/views/layouts/app.blade.php`: public marketplace layout.
- `resources/views/layouts/admin.blade.php`: separate admin dashboard layout.
- `resources/views/layouts/admin-auth.blade.php`: separate admin login layout.
- `resources/views`: Blade pages for landing, explore, auction detail, user dashboard, profile, notifications, bids, wins, watchlist, and admin screens.
- `resources/js`: Alpine.js, dark/light mode, Laravel Echo/Pusher, and countdown timer logic.
- `routes/web.php`: RESTful web routes.
- `routes/channels.php`: broadcasting channel authorization.

## Setup

```bash
composer install
npm install
copy .env.example .env
php artisan key:generate
```

Create a MySQL database named `auction_db`, then verify `.env`:

```env
APP_NAME="AuctionPro"
APP_URL=http://127.0.0.1:8000
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=auction_db
DB_USERNAME=root
DB_PASSWORD=

BROADCAST_CONNECTION=pusher
QUEUE_CONNECTION=database
FILESYSTEM_DISK=public

PUSHER_APP_ID=your-app-id
PUSHER_APP_KEY=your-key
PUSHER_APP_SECRET=your-secret
PUSHER_APP_CLUSTER=mt1
PUSHER_SCHEME=https
VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
```

Run the app:

```bash
php artisan migrate --seed
php artisan storage:link
npm run build
php artisan serve
php artisan queue:work
php artisan schedule:work
```

Demo accounts after seeding:

- Admin: `admin@example.com` / `password`
- User: `test@example.com` / `password`

For local development with hot reload:

```bash
npm run dev
```

## Main Routes

- `GET /`: home page
- `GET /auctions`: auction listing with search, category, status, and price filters
- `GET /auctions/{auction}`: auction details, countdown, bids
- `GET|POST /user/register`: user registration using the user guard
- `GET|POST /user/login`: user login using the user guard
- `GET|POST /admin/login`: admin login using the admin guard
- `GET /register` and `GET /login`: legacy redirects to user auth
- `POST /user/logout`: user logout
- `POST /admin/logout`: admin logout
- `GET /forgot-password`, `POST /forgot-password`, `GET /reset-password/{token}`, `POST /reset-password`
- `GET /user/dashboard`: user dashboard
- `GET /user/notifications`: notification center
- `GET /user/my-bids`: bidding history
- `GET /user/won-auctions`: won auctions
- `GET /user/watchlist`: watchlist/favorites
- `GET|POST /sell-requests`: submit and track seller requests
- `GET|PUT /sell-requests/{auction_request}/edit`: edit pending seller requests
- `GET|PATCH /profile`: profile management
- `GET|POST /auctions/create`: create auction
- `GET|PUT|DELETE /auctions/{auction}`: edit/delete before start
- `POST /auctions/{auction}/bids`: place bid
- `POST /auctions/{auction}/watch`: toggle watchlist
- `GET /admin/dashboard`: admin statistics
- `GET /admin/users`: manage users and roles
- `GET /admin/auctions`: remove fraudulent auctions
- `GET|POST /admin/categories`: manage categories
- `GET /admin/bids`: monitor bids
- `GET /admin/reports`: reports and top auction analytics
- `GET /admin/seller-requests`: approve/reject user submitted listings
- `GET|POST /admin/auctions/create`: admin-created auctions publish directly

No separate JSON API is included by default; the app uses RESTful web routes. The bidding endpoint can be adapted to JSON by returning `response()->json()` from `BidController`.

## Auction Workflow

Public auctions have three dynamic states based on time:

- Active: approved, started, not ended, and open for bidding.
- Upcoming: approved, start time is in the future, visible publicly, bidding disabled.
- Closed: approved, ended or manually closed, visible for history, bidding disabled, winner shown when available.

Users cannot directly publish auctions. They submit sell requests from `/sell-requests/create`; pending and rejected requests are visible only to the seller and admin. Admins review requests at `/admin/seller-requests`; approval creates a public auction, while rejection stores moderation notes.

Admins can also create auctions directly from `/admin/auctions/create`; these are approved immediately and appear publicly according to their start/end times.

## ER Diagram Explanation

`users` has many `auctions` and many `bids`. `users.role` controls admin/user access. `categories` has many `auctions`. `auctions` belongs to a seller user and category, has many bids, and optionally belongs to a winner user. `bids` belongs to one user and one auction. `notifications` stores Laravel database notifications for outbid, winner, and ending-soon messages.

Text ER view:

```text
users 1---* auctions
users 1---* bids
users 1---* auctions.winner_id
categories 1---* auctions
auctions 1---* bids
users 1---* notifications
```

## Real-Time Bidding

`BidController` validates the auction state, locks the auction row inside a database transaction, checks the minimum bid increment, creates the bid, updates `current_price`, notifies the previous highest bidder, and broadcasts `BidPlaced` on `auctions.{id}`. `resources/js/app.js` listens with Laravel Echo and updates the current price and bid history without refreshing.

## Notifications

- `OutbidNotification`: sent to the previous highest bidder.
- `AuctionWonNotification`: sent when an ended auction is closed and has a winner.
- `AuctionEndingSoonNotification`: sent by `php artisan schedule:work` when an active auction is within one hour of ending.

## Future Scope

- Payment integration and invoices.
- Seller verification and fraud scoring.
- Auto-bidding/proxy bidding.
- Watchlists and saved searches.
- Admin reports with charts.
- API tokens for mobile apps.
- Image moderation and multi-image galleries.
- WebSocket server with Laravel Reverb for self-hosting.

## Viva Questions and Answers

1. What is the purpose of Eloquent relationships here?
   They let Laravel express domain links such as a user having many auctions and an auction having many bids, which makes queries readable and consistent.

2. Why use Form Request classes?
   They keep validation rules outside controllers and make create/update/bid validation reusable and beginner-friendly.

3. How is role-based access implemented?
   Users have a `role` column. The `admin` middleware checks `isAdmin()` before allowing admin panel routes.

4. How is bid consistency protected?
   The bid action uses a database transaction and `lockForUpdate()` so two users cannot safely overwrite each other with stale highest-bid values.

5. What does broadcasting do?
   It pushes the latest bid data to connected browsers through Pusher so users see updated prices immediately.

6. Why use notifications?
   Notifications provide a standard Laravel way to store and email important events like being outbid or winning.

7. How are auctions closed?
   The `auctions:close-ended` command runs every minute through Laravel Scheduler and also closes an auction when its detail page is viewed after the end time.

8. What is a policy?
   A policy centralizes authorization rules. `AuctionPolicy` allows owners/admins to update/delete only when the auction is still editable.

9. Why use pagination?
   Pagination keeps listing, dashboard, and admin pages fast and readable as records grow.

10. How would you make a mobile app for this project?
    Add authenticated JSON API routes using Laravel Sanctum, then reuse the same models, policies, events, and notifications.
