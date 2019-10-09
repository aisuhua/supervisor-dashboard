
## Backup

```bash
mysqldump -h localhost -u username -ppassword --no-data supervisor_dashboard  > supervisor_dashboard.sql
```

## Restore

```bash
mysqladmin -u username -ppassword create supervisor_dashboard
mysql -u username -ppassword supervisor_dashboard < supervisor_dashboard.sql  
```