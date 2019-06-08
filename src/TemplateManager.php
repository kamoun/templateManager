<?php

class TemplateManager
{
    public function getTemplateComputed(Template $tpl, array $data)
    {
        if (!$tpl) {
            throw new \RuntimeException('no tpl given');
        }

        if (!empty($data)) {
            $tpl->setSubject($this->computeText($tpl->getSubject(), $data));
            $tpl->setContent($this->computeText($tpl->getContent(), $data));
        }

        return $tpl;
    }

    private function computeText($text, array $data)
    {
        $APPLICATION_CONTEXT = ApplicationContext::getInstance();

        /*
         * USER
         * [user:*]
         */
        $_user  = (isset($data['user'])  and ($data['user']  instanceof User))  ? $data['user']  : $APPLICATION_CONTEXT->getCurrentUser();
        if($_user) {
            (strpos($text, '[user:first_name]') !== false) and $text = str_replace('[user:first_name]'       , ucfirst(mb_strtolower($_user->getFirstname())), $text);
        }

        preg_match('/\:.*\]\w?/', $text, $matches);
        $matches = str_replace(':','',$matches);
        $matches  = str_replace(']','',$matches);

        foreach ($matches as $value) {
            $text = str_replace('[quote:'.$value.']',Faker\Factory::create()->$value,$text);
        }

        return $text;
    }
}
