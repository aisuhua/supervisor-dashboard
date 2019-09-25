
## Export

```bash
mysqldump -h localhost -u root -p --no-data supervisor  > supervisor.sql
```

## Import

```bash
mysqladmin -u username -ppassword create supervisor
mysql -u username -ppassword supervisor < supervisor.sql  
```