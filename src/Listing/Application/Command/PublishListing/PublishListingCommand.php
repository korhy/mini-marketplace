<?php

declare(strict_types=1);

namespace App\Listing\Application\Command\PublishListing;

  final readonly class PublishListingCommand
  {
      public function __construct(
          public string $listingId,
      ) {}
  }
