# Schedules

This module adds schedules for Products, Contents and Store.

## Installation

### Manually

* Copy the module into ```<thelia_root>/local/modules/``` directory and be sure that the name of the module is Schedules.
* Activate it in your thelia administration panel

### Composer

Add it in your main thelia composer.json file

```
composer require your-vendor/schedules-module:~1.0
```

## Usage

Backoffice :
    - module configuration : select which template products and contents should implement in order to have schedules
    - schedules CRUD : in the "Schedules" tab of product, content & store pages
FrontOffice :
    - generates an ics calendar with scheduled products
    - adds a schedule date selection for product purchase, with a stock management system

## Loop

ScheduleLoop : collection of schedules.
ScheduleDateLoop : collection of schedule dates.

## Other ?

If you have other think to put, feel free to complete your readme as you want.
