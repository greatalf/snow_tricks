# Snow_tricks
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/0b6eb5b83e144395919b34993a0e3fd8)](https://app.codacy.com/app/greatalf/snow_tricks?utm_source=github.com&utm_medium=referral&utm_content=greatalf/snow_tricks&utm_campaign=Badge_Grade_Dashboard)

## Installing project :
Copy and paste the .env.dist file, rename it in .env configuring it about line 16 : 
```
DATABASE_URL=mysql://db_user:db_password@127.0.0.1:3306/db_name
```
Normally on Windows, you can make something like that : DATABASE_URL=mysql://root:@127.0.0.1:3306/tricks

For the most great experience, you can do the same thing with line 23, puting your own Gmail email address:
```
MAILER_URL=gmail://username:password@localhost
```

## Configuring Database :
In your favorite **CLI**, copy and paste the following code lines :
Make sure database no exists
```
doctrine:database:drop --force
```
After that, create, migrate and load some fake fixtures
```
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load
```
## Running Server :
Make run your own local server pasting this code :
```
php bin/console server:run
```
Now, you can enjoy that great application clicking [HERE](http://localhost:8000) :)
