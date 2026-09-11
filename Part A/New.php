
<?php

class CallingCardData
{
    public function __construct(
        public string $firstName = 'Juan',
        public string $lastName = 'Dela Cruz',
        public string $businessName = 'College of Computing Studies',
        public string $position = 'BSIT Student',
        public string $street = 'AUF CCS Building',
        public string $city = 'Angeles City',
        public string $website = 'www.auf.edu.ph'
    ) {}

    public function getFullName(): string
    {
        return "{$this->firstName} {$this->lastName}";
    }

    public function getEmail(): string
    {
        return strtolower("{$this->lastName}.{$this->firstName}@auf.edu.ph");
    }

    public function getPhone(): string
    {
        return sprintf('+1 (555) %03d-%04d', rand(100, 999), rand(1000, 9999));
    }

    public function getAddress(): string
    {
        return "{$this->street}, {$this->city}";
    }
}

interface CallingCardBuilderInterface
{
    public function reset(int $width = 1000, int $height = 600): self;
    public function drawBackground(): self;
    public function drawCardFrame(): self;
    public function drawHeader(string $businessName, string $name, string $position): self;
    public function drawDivider(): self;
    public function drawContactInfo(string $email, string $phone, string $address): self;
    public function drawFooter(string $website): self;
    public function build(): GdImage;
}

class GdCallingCardBuilder implements CallingCardBuilderInterface
{
    private GdImage $image;
    private int $colorBackground;
    private int $colorWhite;
    private int $colorBlack;
    private int $colorGray;
    private int $colorBlue;

    public function reset(int $width = 1000, int $height = 600): self
    {
        $this->image = imagecreatetruecolor($width, $height);
        
        $this->colorBackground = imagecolorallocate($this->image, 245, 247, 250);
        $this->colorWhite      = imagecolorallocate($this->image, 255, 255, 255);
        $this->colorBlack      = imagecolorallocate($this->image, 30, 30, 30);
        $this->colorGray       = imagecolorallocate($this->image, 100, 100, 100);
        $this->colorBlue       = imagecolorallocate($this->image, 40, 100, 200);

        return $this;
    }

    public function drawBackground(): self
    {
        imagefill($this->image, 0, 0, $this->colorBackground);
        return $this;
    }

    public function drawCardFrame(): self
    {
        imagefilledrectangle($this->image, 50, 50, 950, 550, $this->colorWhite);
        imagefilledrectangle($this->image, 50, 50, 75, 550, $this->colorBlue);
        return $this;
    }

    public function drawHeader(string $businessName, string $name, string $position): self
    {
        imagestring($this->image, 5, 120, 100, strtoupper($businessName), $this->colorBlue);
        imagestring($this->image, 5, 120, 170, $name, $this->colorBlack);
        imagestring($this->image, 4, 120, 210, $position, $this->colorBlue);
        return $this;
    }

    public function drawDivider(): self
    {
        imageline($this->image, 120, 260, 880, 260, $this->colorGray);
        return $this;
    }

    public function drawContactInfo(string $email, string $phone, string $address): self
    {
        imagestring($this->image, 4, 120, 310, 'Email: ' . $email, $this->colorBlack);
        imagestring($this->image, 4, 120, 365, 'Phone: ' . $phone, $this->colorBlack);
        imagestring($this->image, 4, 120, 420, 'Address: ' . $address, $this->colorBlack);
        return $this;
    }

    public function drawFooter(string $website): self
    {
        imagestring($this->image, 3, 120, 485, $website, $this->colorGray);
        return $this;
    }

    public function build(): GdImage
    {
        return $this->image;
    }
}

class CallingCardDirector
{
    public function constructStandardCard(CallingCardBuilderInterface $builder, CallingCardData $data): GdImage
    {
        return $builder
            ->reset()
            ->drawBackground()
            ->drawCardFrame()
            ->drawHeader($data->businessName, $data->getFullName(), $data->position)
            ->drawDivider()
            ->drawContactInfo($data->getEmail(), $data->getPhone(), $data->getAddress())
            ->drawFooter($data->website)
            ->build();
    }
}

$cardData = new CallingCardData();
$cardImage = (new CallingCardDirector())->constructStandardCard(new GdCallingCardBuilder(), $cardData);

$outputDirectory = __DIR__ . '/cards';

if (!is_dir($outputDirectory) && !mkdir($outputDirectory, 0755, true) && !is_dir($outputDirectory)) {
    throw new RuntimeException("Directory '$outputDirectory' could not be created.");
}

$filename = $outputDirectory . '/calling-card-' . bin2hex(random_bytes(8)) . '.png';

if (imagepng($cardImage, $filename)) {
    echo "Calling card generated successfully.\nFile: $filename\n";
} else {
    echo "ERROR: Could not save image.\n";
}

imagedestroy($cardImage);
