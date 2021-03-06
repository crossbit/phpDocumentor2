<?php
declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2018 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Descriptor\Builder;

/**
 * Attempts to retrieve an Assembler for the provided criteria.
 */
class AssemblerFactory
{
    /** @var AssemblerInterface[] */
    protected $assemblers = [];

    /** @var AssemblerInterface[] */
    protected $fallbackAssemblers = [];

    /**
     * Registers an assembler instance to this factory.
     *
     * @param callable           $matcher   A callback function accepting the criteria as only parameter and which must
     *     return a boolean.
     * @param AssemblerInterface $assembler An instance of the Assembler that will be returned if the callback returns
     *     true with the provided criteria.
     */
    public function register(callable $matcher, AssemblerInterface $assembler): void
    {
        $this->assemblers[] = [
            'matcher' => $matcher,
            'assembler' => $assembler,
        ];
    }

    /**
     * Registers an assembler instance to this factory that is to be executed after all other assemblers have been
     * checked.
     *
     * @param callable           $matcher   A callback function accepting the criteria as only parameter and which must
     *     return a boolean.
     * @param AssemblerInterface $assembler An instance of the Assembler that will be returned if the callback returns
     *     true with the provided criteria.
     */
    public function registerFallback(callable $matcher, AssemblerInterface $assembler): void
    {
        $this->fallbackAssemblers[] = [
            'matcher' => $matcher,
            'assembler' => $assembler,
        ];
    }

    /**
     * Retrieves a matching Assembler based on the provided criteria or null if none was found.
     *
     * @param mixed $criteria
     *
     * @return AssemblerInterface|null
     */
    public function get($criteria)
    {
        foreach (array_merge($this->assemblers, $this->fallbackAssemblers) as $candidate) {
            $matcher = $candidate['matcher'];
            if ($matcher($criteria) === true) {
                return $candidate['assembler'];
            }
        }

        return null;
    }
}
