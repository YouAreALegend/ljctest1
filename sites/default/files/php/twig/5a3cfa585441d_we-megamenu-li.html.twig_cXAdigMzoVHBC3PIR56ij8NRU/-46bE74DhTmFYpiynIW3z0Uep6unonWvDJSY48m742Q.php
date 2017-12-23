<?php

/* modules/contrib/we_megamenu/templates/we-megamenu-li.html.twig */
class __TwigTemplate_aee571b830bcbf4236fc2b141bd906d55a89cb82261fd3ebad69f77525da8205 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $tags = array("if" => 3);
        $filters = array("trim" => 3);
        $functions = array();

        try {
            $this->env->getExtension('Twig_Extension_Sandbox')->checkSecurity(
                array('if'),
                array('trim'),
                array()
            );
        } catch (Twig_Sandbox_SecurityError $e) {
            $e->setSourceContext($this->getSourceContext());

            if ($e instanceof Twig_Sandbox_SecurityNotAllowedTagError && isset($tags[$e->getTagName()])) {
                $e->setTemplateLine($tags[$e->getTagName()]);
            } elseif ($e instanceof Twig_Sandbox_SecurityNotAllowedFilterError && isset($filters[$e->getFilterName()])) {
                $e->setTemplateLine($filters[$e->getFilterName()]);
            } elseif ($e instanceof Twig_Sandbox_SecurityNotAllowedFunctionError && isset($functions[$e->getFunctionName()])) {
                $e->setTemplateLine($functions[$e->getFunctionName()]);
            }

            throw $e;
        }

        // line 1
        echo "<li ";
        echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, ($context["attributes"] ?? null), "html", null, true));
        echo ">
  <a class=\"we-mega-menu-li\" href=\"";
        // line 2
        echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, ($context["href"] ?? null), "html", null, true));
        echo "\">
    ";
        // line 3
        if (($this->getAttribute(($context["attributes"] ?? null), "data-icon", array(), "any", true, true) && twig_trim_filter($this->getAttribute(($context["attributes"] ?? null), "data-icon")))) {
            // line 4
            echo "      <i class=\"";
            echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->getAttribute(($context["attributes"] ?? null), "data-icon"), "html", null, true));
            echo "\"></i>
    ";
        }
        // line 6
        echo "
    ";
        // line 7
        echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, ($context["title"] ?? null), "html", null, true));
        echo "

    ";
        // line 9
        if (($this->getAttribute(($context["attributes"] ?? null), "data-caption", array(), "any", true, true) && twig_trim_filter($this->getAttribute(($context["attributes"] ?? null), "data-caption")))) {
            // line 10
            echo "      <span class=\"we-mega-menu-caption\">";
            echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->getAttribute(($context["attributes"] ?? null), "data-caption"), "html", null, true));
            echo "</span>
    ";
        }
        // line 12
        echo "  </a>
  ";
        // line 13
        echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, ($context["content"] ?? null), "html", null, true));
        echo "
</li>";
    }

    public function getTemplateName()
    {
        return "modules/contrib/we_megamenu/templates/we-megamenu-li.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  79 => 13,  76 => 12,  70 => 10,  68 => 9,  63 => 7,  60 => 6,  54 => 4,  52 => 3,  48 => 2,  43 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "modules/contrib/we_megamenu/templates/we-megamenu-li.html.twig", "/var/www/html/ljctest1/modules/contrib/we_megamenu/templates/we-megamenu-li.html.twig");
    }
}
