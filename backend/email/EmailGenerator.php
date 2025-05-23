<?php

class EmailGenerator
{
    private static function loadFile(string $path): string
    {
        if (!file_exists($path)) {
            return "<p>File non trovato: $path</p>";
        }
        return file_get_contents($path);
    }

    public static function generateEmail(string $templateName, array $data): string
    {
        $header = self::loadFile("email/email_header.html");
        $footer = self::loadFile("email/email_footer.html");
        $body = self::loadTemplate($templateName, $data);

        return $header . $body . $footer;
    }

    private static function loadTemplate(string $templateName, array $values): string
    {
        $templatePath = "email/templates/$templateName.html";
        $template = self::loadFile($templatePath);

        // Sostituisci i segnaposto con i valori
        foreach ($values as $key => $value) {
            $template = str_replace('{{' . $key . '}}', $value, $template);
        }

        return $template;
    }
}
