#!/usr/bin/env bash

echo "🧪 Sky Session - Test Suite"
echo "============================"
echo ""

# Cores
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Verificar se vendor existe
if [ ! -d "vendor" ]; then
    echo -e "${YELLOW}⚠ Vendor não encontrado. Instalando dependências...${NC}"
    composer install
    echo ""
fi

# 1. PHPUnit
echo -e "${YELLOW}1. Executando testes...${NC}"
./vendor/bin/phpunit
PHPUNIT_EXIT=$?
echo ""

# 2. PHPStan
echo -e "${YELLOW}2. Análise estática (PHPStan)...${NC}"
./vendor/bin/phpstan analyse src --level=max
PHPSTAN_EXIT=$?
echo ""

# 3. CodeSniffer
echo -e "${YELLOW}3. Verificando code style (PSR-12)...${NC}"
./vendor/bin/phpcs src --standard=PSR12
PHPCS_EXIT=$?
echo ""

# Resultado final
echo "============================"
echo "📊 Resultado Final:"
echo "============================"

if [ $PHPUNIT_EXIT -eq 0 ]; then
    echo -e "✅ ${GREEN}PHPUnit: PASSOU${NC}"
else
    echo -e "❌ ${RED}PHPUnit: FALHOU${NC}"
fi

if [ $PHPSTAN_EXIT -eq 0 ]; then
    echo -e "✅ ${GREEN}PHPStan: PASSOU${NC}"
else
    echo -e "❌ ${RED}PHPStan: FALHOU${NC}"
fi

if [ $PHPCS_EXIT -eq 0 ]; then
    echo -e "✅ ${GREEN}Code Style: PASSOU${NC}"
else
    echo -e "❌ ${RED}Code Style: FALHOU${NC}"
fi

echo ""

# Exit code final
if [ $PHPUNIT_EXIT -eq 0 ] && [ $PHPSTAN_EXIT -eq 0 ] && [ $PHPCS_EXIT -eq 0 ]; then
    echo -e "${GREEN}🎉 Todos os testes passaram!${NC}"
    exit 0
else
    echo -e "${RED}⚠ Alguns testes falharam.${NC}"
    exit 1
fi
