A simple php helper to help work with timezone offsets. 

## Usage

```php

use hanakivan\Timezones;

$timezone = new Timezones('America/Los_Angeles');

echo $timezone->timezoneName; // => prints 'America/Los_Angeles'
echo $timezone->getOffset(); // => prints 'GMT-07:00'
echo $timezone->getOffset(false); // => prints '-25200'

```