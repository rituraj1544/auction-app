# Project Name: Real-Time Online Auction Platform
## Software Requirements Specification

**Course Code**: INT221
**Course Name**: [Insert Course Name]

**Student Names**: [Insert Student Names]
**Student Registration Numbers**: [Insert Registration Numbers]

**Prepared for**: Continuous Assessment 3
**Spring 2025**

---

## Table of Contents
1. INTRODUCTION
   1.1 Purpose
   1.2 Scope
   1.3 Definitions, Acronyms, and Abbreviations
   1.4 References
   1.5 Overview
2. GENERAL DESCRIPTION
   2.1 Product Perspective
   2.2 Product Functions
   2.3 User Characteristics
   2.4 General Constraints
   2.5 Assumptions and Dependencies
3. SPECIFIC REQUIREMENTS
   3.1 External Interface Requirements
   3.2 Functional Requirements
   3.5 Non-Functional Requirements
   3.7 Design Constraints
   3.9 Other Requirements
4. ANALYSIS MODELS
   4.1 Data Flow Diagrams (DFD) / ER Models
5. GITHUB LINK
6. DEPLOYED LINK
7. CLIENT APPROVAL PROOF
8. CLIENT LOCATION PROOF
9. TRANSACTION ID PROOF
10. EMAIL ACKNOWLEDGEMENT
11. GST No.

---

## 1. Introduction

### 1.1 Purpose
The purpose of this Software Requirement Specification (SRS) is to document the requirements for the Real-Time Online Auction Platform. This document provides a comprehensive description of the software product, its functionalities, performance goals, and design constraints, serving as a guideline for developers, evaluators, and stakeholders.

### 1.2 Scope
The software product to be produced is the **Real-Time Online Auction Platform**. 
This application provides a comprehensive online marketplace environment where:
- Users can browse items, manage watchlists, and place bids in real-time.
- Sellers can submit requests to auction their items.
- Administrators can review and moderate seller requests, monitor bids, and oversee user activity through a dedicated SaaS-style dashboard.
The platform will heavily utilize WebSockets for live bidding updates, preventing the need for manual page reloads and ensuring an engaging auction experience. The current scope handles the core auction and bidding mechanisms; payment gateway integration and physical shipping logistics are outside the current scope.

### 1.3 Definitions, Acronyms, and Abbreviations
- **SRS**: Software Requirements Specification
- **ER**: Entity-Relationship
- **WebSocket**: A computer communications protocol providing full-duplex communication channels over a single TCP connection, used here for real-time bidding.
- **ORM**: Object-Relational Mapping (e.g., Laravel Eloquent).

### 1.4 References
- Laravel framework documentation
- Tailwind CSS and Alpine.js documentation
- Pusher API references for real-time event broadcasting

### 1.5 Overview
The rest of this SRS breaks down the general operational environment of the system, followed by specific requirements including user interfaces, core functional features, and non-functional attributes such as performance and security.

---

## 2. General Description

### 2.1 Product Perspective
The Real-Time Online Auction Platform operates as an independent web application. It replaces traditional manual auction processes and static marketplace listings by introducing dynamic, real-time bid updates, structured moderation workflows, and automated scheduled event handlers for auction life-cycles.

### 2.2 Product Functions
A summary of major functions includes:
- **Authentication:** Separate login mechanisms and guards for regular Users and Administrators.
- **Marketplace Browsing:** Searching, sorting, and filtering auctions by category, status, and price.
- **Seller Workflow:** Users submit "Sell Requests" which admins approve or reject.
- **Bidding System:** Real-time bidding with automatic validation and price updates.
- **Notifications:** In-app and email alerts for outbidding, winning, and auctions ending soon.
- **Analytics & Moderation:** Admin dashboard for reviewing fraudulent activity, approving seller requests, and generating reports.

### 2.3 User Characteristics
- **Standard Users (Buyers/Sellers):** Expected to be familiar with typical e-commerce and marketplace environments. Requires an intuitive interface for managing bids, tracking watchlists, and submitting items.
- **Administrators:** Trusted staff requiring an efficient dashboard to manage categories, user roles, bids, and reported auctions.

### 2.4 General Constraints
- The backend must be developed using Laravel (PHP).
- Data must be managed using a relational database (MySQL).
- Requires an active worker process (`queue:work`) and scheduler (`schedule:work`) running on the server for asynchronous jobs and timers.

### 2.5 Assumptions and Dependencies
- **Assumptions:** Users have access to modern web browsers with JavaScript enabled to support Alpine.js and WebSockets.
- **Dependencies:** Relies on third-party WebSocket broadcasting services (e.g., Pusher or Laravel Reverb) to handle real-time concurrency.

---

## 3. Specific Requirements

### 3.1 External Interface Requirements

#### 3.1.1 User Interfaces
- **Public Interface:** A marketplace layout built with Tailwind CSS, featuring light/dark modes, responsive design for mobile accessibility, and live countdown timers.
- **Admin Interface:** A separate administrative dashboard layout protected by admin-specific authentication middleware.

#### 3.1.2 Hardware Interfaces
- **Server:** A standard web server environment capable of hosting PHP 8+ and MySQL.
- **Client:** Compatible with PC, Mac, tablets, and smartphones.

#### 3.1.3 Software Interfaces
- **Database:** MySQL database `auction_db` to store users, categories, auctions, bids, and notifications.
- **Broadcasting:** Integration with Pusher for real-time event broadcasting (`BidPlaced` event).

#### 3.1.4 Communications Interfaces
- Secure HTTP (HTTPS) for all web traffic to protect user credentials.
- WebSockets Secure (WSS) to maintain persistent connections for live bid streams.

### 3.2 Functional Requirements

#### 3.2.1 Real-Time Bidding Feature
- **3.2.1.1 Introduction:** Allows users to place competing bids on active auctions, with the highest bid broadcasted instantly to all observers.
- **3.2.1.2 Inputs:** Bid amount entered by an authenticated user.
- **3.2.1.3 Processing:** The system validates the auction state (Active), initiates a database transaction with a pessimistic lock (`lockForUpdate`) to prevent race conditions, verifies the bid exceeds the current price plus minimum increment, records the bid, and updates the auction's `current_price`.
- **3.2.1.4 Outputs:** A `BidPlaced` broadcast event is fired to update the UI via Laravel Echo. A notification is queued for the previously highest bidder.
- **3.2.1.5 Error Handling:** If the bid is too low or the auction is closed, the transaction is rolled back and a validation error is returned to the user.

#### 3.2.2 Seller Moderation Workflow
- **3.2.2.1 Introduction:** Users cannot directly publish listings; they must submit sell requests for administrative review.
- **3.2.2.2 Inputs:** Item title, description, starting price, category, start/end dates.
- **3.2.2.3 Processing:** The request is saved in a pending state. Admins view the request and either approve (creating a public auction record) or reject (storing moderation feedback).
- **3.2.2.4 Outputs:** Updated status visible in the user's dashboard.

### 3.5 Non-Functional Requirements

#### 3.5.1 Performance
- Real-time bid broadcasts should reflect on connected clients within 1 second of submission.
- The web platform uses pagination to ensure fast page loads even with thousands of listings or bid histories.

#### 3.5.2 Reliability
- Database transactions ensure that even under high concurrency, two users cannot simultaneously overwrite each other's bids with stale pricing data.

#### 3.5.3 Availability
- The system should maintain high availability, relying on standard load-balancing techniques. Scheduled tasks for closing auctions must run reliably every minute.

#### 3.5.4 Security
- Form submissions are secured using CSRF tokens and validated using Laravel Form Request classes.
- Access control is enforced via Laravel Policies (e.g., `AuctionPolicy`) ensuring users can only edit or delete their own items before bidding begins.
- Role-based access ensures regular users cannot access administrative REST endpoints.

#### 3.5.5 Maintainability
- Code follows standard MVC principles. Business logic is organized into Controllers, database queries are managed by Eloquent Models and Relationships, and validation is abstracted into Request classes.

#### 3.5.6 Portability
- The application can easily be dockerized or moved between standard Linux hosting environments using `composer install` and `php artisan migrate`.

### 3.7 Design Constraints
- Must utilize Laravel Eloquent relationships for domain linking (e.g., users having many auctions, auctions having many bids) to ensure code readability and consistency.

### 3.9 Other Requirements
- Automated CRON jobs required for `auctions:close-ended` to automatically conclude auctions and assign winners.

---

## 4. Analysis Models

### 4.1 Data Flow Diagrams (DFD) / ER Models
The core Entity-Relationship model revolves around users, auctions, and bids:
- `users` 1 --- * `auctions` (A user can list many auctions)
- `users` 1 --- * `bids` (A user can place many bids)
- `users` 1 --- * `auctions.winner_id` (A user can win many auctions)
- `categories` 1 --- * `auctions` (A category contains many auctions)
- `auctions` 1 --- * `bids` (An auction receives many bids)
- `users` 1 --- * `notifications` (A user receives many system alerts)

---

## 5. GITHUB LINK
*(Student to provide link)*

## 6. DEPLOYED LINK
*(Student to provide link)*

## 7. CLIENT APPROVAL PROOF
*(Student to attach/provide proof)*

## 8. CLIENT LOCATION PROOF
*(Student to attach/provide proof)*

## 9. TRANSACTION ID PROOF
*(Student to attach/provide proof)*

## 10. EMAIL ACKNOWLEDGEMENT
*(Student to attach/provide proof)*

## 11. GST No.
*(Student to provide GST number if applicable)*
