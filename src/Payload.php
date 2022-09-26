<?php

namespace Webcomcafe\Pix;

class Payload
{
    /**
     * Valor a ser transformado em qr
     *
     * @var string $pixCopiaECola
     */
    private $pixCopiaECola;

    /**
     * @var string $level
     */
    private $level;

    /**
     * Class que representa saída do qrcode
     *
     * \Mpdf\QrCode\Output\Png
     * \Mpdf\QrCode\Output\Svg
     * \Mpdf\QrCode\Output\Html
     *
     * @var string|null
     */
    private $output;

    /**
     * @param string $pixCopiaECola
     * @param string $level
     * @param string|null $output
     */
    public function __construct(string $pixCopiaECola, string $level = 'L', string $output = \Mpdf\QrCode\Output\Png::class)
    {
        $this->pixCopiaECola = $pixCopiaECola;
        $this->level = $level;
        $this->output = $output;
    }

    /**
     * Retorna o valor informado para geração do qrcode
     *
     * @return string
     */
    public function getValue(): string
    {
        return $this->pixCopiaECola;
    }

    /**
     * Retorna o qrcode gerado em base64, pronto para ser exibido em imagem
     *
     * @param int $w
     * @return string
     * @throws \Mpdf\QrCode\QrCodeException
     */
    public function toQrCode(int $w = 300): string
    {
        $qr = new \Mpdf\QrCode\QrCode($this->pixCopiaECola, $this->level);

        $out = new $this->output;

        $content = $out->output($qr, $w);

        return base64_encode($content);
    }
}