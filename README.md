# TradeShop

TradeShop is a full-featured e-commerce web application that allows administrators and sellers to manage products, orders, customers, deliveries, and sales statistics. It provides an intuitive dashboard for monitoring and managing all business activities.

## Table of Contents
- [Features](#features)
- [Technologies Used](#technologies-used)
- [Installation](#installation)
- [Configuration](#configuration)
- [Running the Application](#running-the-application)
- [Project Structure](#project-structure)
- [Contributing](#contributing)
- [License](#license)

## Features
- Product management: add, edit, delete, manage stock
- Category management
- User management: employees, delivery staff, sellers
- Customer management and order tracking
- Delivery management and assignment of delivery personnel
- Sales statistics and reports (top products, sales by period)
- Withdrawals via Campay API (mobile payments)
- Interactive admin dashboard

## Technologies Used
- **Backend:** Node.js, Express.js
- **Database:** PostgreSQL, Sequelize ORM
- **Frontend:** EJS, HTML, CSS, JavaScript, Chart.js
- **Authentication & Security:** Sessions, CSRF protection
- **External API:** Campay (mobile payment)

## Installation

### Clone the repository
```bash
git clone https://github.com/Dylan429-eng/TradeShop.git
cd TradeShop
```

### Install dependencies
```bash
npm install
```

## Configuration

Create a `.env` file at the root with the following variables:
```env
DB_HOST=localhost
DB_USER=your_db_user
DB_PASS=your_db_password
DB_NAME=your_db_name
PORT=3000
SESSION_SECRET=your_secret
CAMPAY_TOKEN=your_campay_token
```

Initialize the database (using Sequelize CLI or migration scripts).

## Running the Application
```bash
npm start
```
The application will be available at: [http://localhost:3000](http://localhost:3000)

## Project Structure
```
TradeShop/
├─ controllers/          # Route handlers and business logic
├─ models/               # Sequelize models
├─ public/               # Static assets (CSS, JS, images)
├─ routes/               # Express routes
├─ views/                # EJS templates
├─ utils/                # Helper functions (e.g., emails)
├─ .env                  # Environment variables
├─ app.js                # Main Express app
└─ package.json
```

## Contributing

1. Fork the repository
2. Create a new feature branch:
   ```bash
   git checkout -b feature/your-feature
   ```
3. Commit your changes:
   ```bash
   git commit -m "Add your feature description"
   ```
4. Push to your branch:
   ```bash
   git push origin feature/your-feature
   ```
5. Open a Pull Request

## License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for