<?php

namespace Stoyishi\Bot\Types;

class Location
{
    private array $data;
    public ?float $latitude = null;
    public ?float $longitude = null;
    public ?float $horizontalAccuracy = null;
    public ?int $livePeriod = null;
    public ?int $heading = null;
    public ?int $proximityAlertRadius = null;
    
    public function __construct(array $data)
    {
        $this->data = $data;
        $this->latitude = $data['latitude'] ?? null;
        $this->longitude = $data['longitude'] ?? null;
        $this->horizontalAccuracy = $data['horizontal_accuracy'] ?? null;
        $this->livePeriod = $data['live_period'] ?? null;
        $this->heading = $data['heading'] ?? null;
        $this->proximityAlertRadius = $data['proximity_alert_radius'] ?? null;
    }
    
    public function getLatitude(): ?float
    {
        return $this->latitude;
    }
    
    public function getLongitude(): ?float
    {
        return $this->longitude;
    }
    
    public function getData(): array
    {
        return $this->data;
    }
}