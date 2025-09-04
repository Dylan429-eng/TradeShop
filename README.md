# TradeShop -  E-commerce Application

Welcome to the TradeShop repository, a complete e-commerce application built with Laravel 12.  
This project offers advanced management of products, orders, clients, employees, delivery agents, and statistics, with a modern and responsive interface.

## Main Features

- **Product Management**: Add, edit, delete, categorize products.
- **Order Management**: Track orders, assign to delivery agents, withdrawals, history.
- **Client Management**: View clients, statistics, order history.
- **Employee Management**: View, delete, manage roles (admin, seller, delivery agent).
- **Delivery Agent Management**: Dedicated dashboard, delivery tracking, assigned orders.
- **Advanced Statistics**: Best/worst selling products, period filters, Chart.js graphs.
- **Notifications**: Instant Gmail email sent to delivery agent when an order is assigned.
- **Security**: Authentication, email verification, role-based middlewares.
- **Responsive Design**: Interface adapts to desktop and mobile.

## Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/your-username/tradeshop.git
   cd tradeshop
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install && npm run build
   ```

3. **Configure environment**
   - Copy `.env.example` to `.env`
   - Set your DB and Gmail credentials (for notifications)

4. **Generate application key**
   ```bash
   php artisan key:generate
   ```

5. **Run database migrations**
   ```bash
   php artisan migrate --seed
   ```

6. **Start the server**
   ```bash
   php artisan serve
   ```

## Gmail Configuration (notifications)

In your `.env` file:
```
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_gmail@gmail.com
MAIL_PASSWORD=your_gmail_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your_gmail@gmail.com
MAIL_FROM_NAME="TradeShop"
```

## Role Structure

- **Admin**: Full access to management and statistics.
- **Delivery Agent**: Personal dashboard, access to assigned orders.
- **Seller/Employee**: Manage products and orders according to their role.

## Contribution

Pull requests and suggestions are welcome!  
Please respect the project structure and add tests when possible.

## License

This project is licensed under the MIT License.

---

**TradeShop** – Complete and scalable Laravel e-
