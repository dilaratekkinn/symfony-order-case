<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;


/**
 * @ORM\Entity(repositoryClass=OrderRepository::class)
 * @ORM\Table(name="`order`")
 */
class Order
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @JMS\Groups({"order"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="orders")
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity=OrderItem::class, mappedBy="belongsToOrder")
     * @JMS\Groups({"order"})
     */
    private $orderItem;

    /**
     * @ORM\OneToOne(targetEntity=Discount::class, cascade={"persist", "remove"})
     * @JMS\Groups({"order"})
     */
    private $discount;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=0)
     * @JMS\Groups({"order"})
     */
    private $total;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=0)
     * @JMS\Groups({"order"})
     */
    private $discount_price;

    /**
     * @ORM\Column(type="string", length=255)
     * @JMS\Groups({"order"})
     */
    private $status;

    public function __construct()
    {
        $this->orderItem = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, OrderItem>
     */
    public function getOrderItem(): Collection
    {
        return $this->orderItem;
    }

    public function addOrderItem(OrderItem $orderItem): self
    {
        if (!$this->orderItem->contains($orderItem)) {
            $this->orderItem[] = $orderItem;
            $orderItem->setBelongsToOrder($this);
        }

        return $this;
    }

    public function removeOrderItem(OrderItem $orderItem): self
    {
        if ($this->orderItem->removeElement($orderItem)) {
            // set the owning side to null (unless already changed)
            if ($orderItem->getBelongsToOrder() === $this) {
                $orderItem->setBelongsToOrder(null);
            }
        }

        return $this;
    }

    public function getDiscount(): ?Discount
    {
        return $this->discount;
    }

    public function setDiscount(?Discount $discount): self
    {
        $this->discount = $discount;

        return $this;
    }

    public function getTotal(): ?string
    {
        return $this->total;
    }

    public function setTotal(string $total): self
    {
        $this->total = $total;

        return $this;
    }

    public function getDiscountPrice(): ?string
    {
        return $this->discount_price;
    }

    public function setDiscountPrice(string $discount_price): self
    {
        $this->discount_price = $discount_price;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }
}
