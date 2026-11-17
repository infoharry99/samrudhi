# samrudhi
# SHRI SAMRUDDHI SOCIETY
## Society Management & Loan Accounting System

A comprehensive Web and Mobile PWA Society Management & Loan Accounting System replacing the manual paper register with digital accuracy, real-time ledgers, physical register replica layouts, and an isolated member portal.

---

### Features
- **Physical Register Replica:** Exact paper register layout with automatic formula calculations:
  `Monthly Payable = Monthly Deposit + Interest + Monthly Installment + Penalty`
- **Loan Accounting:** Configurable reducing balance interest calculations, EMI schedules, and principal tracking.
- **Strict Accounting Rule:** Principal installment repayment strictly reduces principal balance; interest payments never reduce principal.
- **PWA Mobile App:** Native-app feel with installable manifest, offline service worker, and bottom thumb navigation.
- **Member Portal:** Strict data isolation; members log in with mobile number to view their own savings passbooks, loan statements, and pay dues online.
- **Cash & Online Collections:** Cash collection receipt generator and online UPI QR checkout.
- **Automated Data Migration:** Includes migration tool to import existing physical registers via CSV.

---

### One-Click Deployment Route
To run migrations and seed the initial 28 members on your live server, execute:
```bash
# Web / Browser:
https://your-domain.com/deploy?token=samrudhi2026

# Or Direct Webhook / Curl:
curl -X POST "https://your-domain.com/deploy/migrate-seed?token=samrudhi2026"
```

### Default Credentials
- **Admin Portal:** Mobile: `9876543210` | Password: `admin123`
- **Member Portal:** Mobile: `9825000001` | Password: `member123`
